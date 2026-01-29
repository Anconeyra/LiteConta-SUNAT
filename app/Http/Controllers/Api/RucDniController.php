<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RucDniController extends Controller
{
    public function consultarRuc($ruc)
    {
        $token = config('services.apisperu.token', env('APIS_PERU_TOKEN'));
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de API no configurado'
            ], 500);
        }

        try {
            $response = Http::get("https://dniruc.apisperu.com/api/v1/ruc/{$ruc}", [
                'token' => $token
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['error'])) {
                    return response()->json([
                        'success' => false,
                        'message' => $data['error']
                    ], 404);
                }
                
                // Verificar si la empresa está activa
                $isActive = isset($data['estado']) && 
                           (stripos($data['estado'], 'ACTIVO') !== false || stripos($data['estado'], 'HABIDO') !== false) &&
                           isset($data['condicion']) && 
                           stripos($data['condicion'], 'HABIDO') !== false;
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'document_number' => $data['ruc'] ?? $ruc,
                        'name' => $data['razonSocial'] ?? 'Nombre no disponible',
                        'address' => $data['direccion'] ?? 'Dirección no disponible',
                        'status_sunat' => $data['estado'] ?? 'ESTADO NO DISPONIBLE',
                        'condition_sunat' => $data['condicion'] ?? 'CONDICION NO DISPONIBLE',
                        'is_active' => $isActive
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener información del RUC'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el RUC: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function consultarDni($dni)
    {
        $token = config('services.apisperu.token', env('APIS_PERU_TOKEN'));
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de API no configurado'
            ], 500);
        }

        try {
            $response = Http::get("https://dniruc.apisperu.com/api/v1/dni/{$dni}", [
                'token' => $token
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['error'])) {
                    return response()->json([
                        'success' => false,
                        'message' => $data['error']
                    ], 404);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'document_number' => $data['dni'] ?? $dni,
                        'name' => trim(($data['nombres'] ?? '') . ' ' . 
                                 ($data['apellidoPaterno'] ?? '') . ' ' . 
                                 ($data['apellidoMaterno'] ?? '')),
                        'address' => 'Dirección no disponible',
                        'status_sunat' => 'NO APLICA',
                        'condition_sunat' => 'NO APLICA',
                        'is_active' => true
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener información del DNI'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el DNI: ' . $e->getMessage()
            ], 500);
        }
    }
}