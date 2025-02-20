<?php

namespace App\Repositories\Actions;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\User;
use App\Models\UserRole;
use App\Models\Zones;
use App\Models\Site;
use App\Models\SalesType;

//TRAITS

class ActionsRepository
{
    public function getZones($request)
    {
        try {
            $zones = Zones::where('destination_id', 1)->get();

            if ( empty($zones) ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'data',
                'data' => $zones
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSites($request)
    {
        try {
            $sites = Site::alll();

            if ( empty($sites) ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'data',
                'data' => $sites
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTypeSales($request)
    {
        try {
            $typeSales = SalesType::alll();

            if ( empty($typeSales) ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'data',
                'data' => $typeSales
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}