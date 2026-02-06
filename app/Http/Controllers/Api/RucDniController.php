<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RucDniController extends Controller
{
    /**
     * Limpia una cadena de texto removiendo caracteres especiales innecesarios
     * y normalizando espacios
     */
    private function cleanString($str)
    {
        // Remover caracteres de control y reemplazar múltiples espacios con uno solo
        $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $str); // Remover caracteres de control
        $clean = preg_replace('/\s+/', ' ', $clean); // Reemplazar múltiples espacios con uno solo
        return trim($clean);
    }
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

                // Limpiar y formatear los datos
                $documentNumber = trim($data['ruc'] ?? $ruc);
                $name = trim($this->cleanString($data['razonSocial'] ?? 'Nombre no disponible'));
                $address = trim($this->cleanString($data['direccion'] ?? 'Dirección no disponible'));
                $statusSunat = trim($data['estado'] ?? 'ESTADO NO DISPONIBLE');
                $conditionSunat = trim($data['condicion'] ?? 'CONDICION NO DISPONIBLE');

                return response()->json([
                    'success' => true,
                    'data' => [
                        'document_number' => $documentNumber,
                        'name' => $name,
                        'address' => $address,
                        'status_sunat' => $statusSunat,
                        'condition_sunat' => $conditionSunat,
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

                // Limpiar y formatear los datos
                $documentNumber = trim($data['dni'] ?? $dni);
                $fullName = trim($this->cleanString(($data['nombres'] ?? '') . ' ' .
                                 ($data['apellidoPaterno'] ?? '') . ' ' .
                                 ($data['apellidoMaterno'] ?? '')));
                $address = 'Dirección no disponible';

                return response()->json([
                    'success' => true,
                    'data' => [
                        'document_number' => $documentNumber,
                        'name' => $fullName,
                        'address' => $address,
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