<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DebugController extends Controller
{
    public function testLapanganApi()
    {
        try {
            $response = Http::timeout(10)->get('http://localhost:8001/api/lapangan');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'success',
                    'response_status' => $response->status(),
                    'data_structure' => [
                        'has_success' => isset($data['success']),
                        'success_value' => $data['success'] ?? null,
                        'has_data' => isset($data['data']),
                        'data_type' => isset($data['data']) ? gettype($data['data']) : 'no_data',
                        'data_keys' => isset($data['data']) && is_array($data['data']) ? array_keys($data['data']) : 'not_array_or_no_data',
                        'lapangan_count' => isset($data['data']['data']) ? count($data['data']['data']) : (isset($data['data']) && is_array($data['data']) ? count($data['data']) : 0),
                    ],
                    'sample_data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'status_code' => $response->status(),
                    'body' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'exception',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
