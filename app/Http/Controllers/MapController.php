<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    protected string $baseUrl = 'https://api.openrouteservice.org';

    /**
     * Optimasi urutan kunjungan beberapa titik.
     * POST /api/route/optimize
     */
    public function optimize(Request $request): JsonResponse
    {
        $request->validate([
            'points'        => 'required|array|min:2',
            'points.*.lat'  => 'required|numeric',
            'points.*.lng'  => 'required|numeric',
        ]);

        $points = $request->input('points');
        $last   = count($points) - 1;

        $jobs = [];
        foreach ($points as $i => $p) {
            if ($i === 0 || $i === $last) continue; // titik awal & akhir jadi 'vehicle', bukan 'job'
            $jobs[] = ['id' => $i, 'location' => [$p['lng'], $p['lat']]]; // ORS pakai urutan [lng, lat]
        }

        $vehicle = [
            'id'      => 1,
            'profile' => 'driving-car',
            'start'   => [$points[0]['lng'], $points[0]['lat']],
            'end'     => [$points[$last]['lng'], $points[$last]['lat']],
        ];

        $response = Http::withHeaders(['Authorization' => config('services.ors.key')])
            ->post("{$this->baseUrl}/optimization", ['jobs' => $jobs, 'vehicles' => [$vehicle]]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal menghitung optimasi rute.'], 502);
        }
        return response()->json($response->json());
    }

    /**
     * Ambil geometri rute + jarak/waktu untuk urutan titik yang sudah fix.
     * POST /api/route/directions
     */
    public function directions(Request $request): JsonResponse
    {
        $request->validate([
            'points'       => 'required|array|min:2',
            'points.*.lat' => 'required|numeric',
            'points.*.lng' => 'required|numeric',
        ]);

        $coordinates = collect($request->input('points'))->map(fn ($p) => [$p['lng'], $p['lat']])->all();

        $response = Http::withHeaders(['Authorization' => config('services.ors.key')])
            ->post("{$this->baseUrl}/v2/directions/driving-car/geojson", ['coordinates' => $coordinates]);

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal mengambil rute.'], 502);
        }
        return response()->json($response->json());
    }
}