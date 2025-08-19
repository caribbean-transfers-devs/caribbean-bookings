<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DualDatabaseController extends Controller
{
    public function statisticsServer()
    {
        echo "CPU Load: " . sys_getloadavg()[0] . "\n";
        echo "Memory: " . memory_get_usage(true)/1024/1024 . " MB\n";
        echo "Memory Peak: " . memory_get_peak_usage(true)/1024/1024 . " MB\n";
        echo "Disk Free: " . disk_free_space("/")/1024/1024/1024 . " GB\n";

        // // 1. Detectar si es NVMe
        // $nvme_check = ejecutarComando('lsblk | grep nvme | head -1');
        // if (!empty($nvme_check)) {
        //     echo "✅ TIPO: NVMe\n";
        //     $modelo = ejecutarComando('lsblk -d -o model | grep -v MODEL | head -1');
        //     echo "📋 Modelo: " . trim($modelo) . "\n";
            
        // // 2. Verificar si es SSD (ROTA=0)
        // } else {
        //     $rota = ejecutarComando('lsblk -d -o rota | grep -v ROTA | head -1');
        //     $rota = trim($rota);
            
        //     if ($rota === '0') {
        //         echo "✅ TIPO: SSD\n";
        //         $modelo = ejecutarComando('lsblk -d -o model | grep -v MODEL | head -1');
        //         echo "📋 Modelo: " . trim($modelo) . "\n";
        //     } else {
        //         echo "⚠️ TIPO: HDD (Disco duro tradicional)\n";
        //         $modelo = ejecutarComando('lsblk -d -o model | grep -v MODEL | head -1');
        //         echo "📋 Modelo: " . trim($modelo) . "\n";
        //     }
        // }

        // // 3. Información adicional
        // echo "\n--- INFORMACIÓN ADICIONAL ---\n";
        // echo "Espacio total: " . ejecutarComando('df -h / | awk \'NR==2 {print $2}\'');
        // echo "Espacio usado: " . ejecutarComando('df -h / | awk \'NR==2 {print $3}\'');
        // echo "Espacio libre: " . ejecutarComando('df -h / | awk \'NR==2 {print $4}\'');
        // echo "Sistema de archivos: " . ejecutarComando('df -T / | awk \'NR==2 {print $2}\'');

        // // 4. Benchmark simple (opcional)
        // echo "\n⏱️ Benchmark rápido de escritura:\n";
        // $start = microtime(true);
        // file_put_contents('/tmp/test_disk_speed.txt', str_repeat('X', 1024000)); // 1MB
        // $time = microtime(true) - $start;
        // echo "Tiempo escritura 1MB: " . round($time, 3) . " segundos\n";
        // unlink('/tmp/test_disk_speed.txt');        
    }

    public function validateSales(){
        $data = [];
        $chunkSize = 1000; // Ajusta según necesidades
        
        DB::connection('mysql_secondary')
            ->table('sales')
            ->orderBy('id') // Importante para el chunking
            ->chunk($chunkSize, function ($sales) use (&$data) {
                $ids = $sales->pluck('id')->toArray();
                
                $existingSales = DB::connection('mysql')
                    ->table('sales')
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->toArray();
                    
                $missingIds = array_diff($ids, $existingSales);
                
                $data = array_merge($data, $missingIds);
            });
        
        return response()->json($data, 200);
    }

    public function validatePayments(){
        $data = [];
        $chunkSize = 1000; // Ajusta según necesidades
        
        DB::connection('mysql_secondary')
            ->table('payments')
            ->orderBy('id') // Importante para el chunking
            ->chunk($chunkSize, function ($sales) use (&$data) {
                $ids = $sales->pluck('id')->toArray();
                
                $existingSales = DB::connection('mysql')
                    ->table('payments')
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->toArray();
                    
                $missingIds = array_diff($ids, $existingSales);
                
                $data = array_merge($data, $missingIds);
            });
        
        return response()->json($data, 200);
    }

    public function validateReservationItems(){
        $data = [];
        $chunkSize = 1000; // Ajusta según necesidades
        
        DB::connection('mysql_secondary')
            ->table('reservations')
            ->orderBy('id') // Importante para el chunking
            ->chunk($chunkSize, function ($sales) use (&$data) {
                $ids = $sales->pluck('id')->toArray();
                
                $existingSales = DB::connection('mysql')
                    ->table('reservations')
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->toArray();
                    
                $missingIds = array_diff($ids, $existingSales);
                
                $data = array_merge($data, $missingIds);
            });
        
        return response()->json([
            "total" => count($data),
            "data"  => $data,            
        ], 200);
    }

    public function validateReservationItems2() {
        $data = [];
        
        $salesIds = DB::connection('mysql_secondary')
            ->table('reservations_follow_up')
            ->pluck('id');
        
        // Procesar en bloques los IDs para evitar memoria
        foreach (array_chunk($salesIds->toArray(), 1000) as $chunk) {
            $existing = DB::connection('mysql')
                ->table('reservations_follow_up')
                ->whereIn('id', $chunk)
                ->pluck('id')
                ->toArray();
                
            $data = array_merge($data, array_diff($chunk, $existing));
        }
        
        return response()->json($data, 200);
    }    

    public function copyMissingReservationItems(Request $request) {
        $missingIds = $request->input('ids', []);
        $chunkSize = 1000; // Ajusta según capacidad del servidor
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        // Validar que se hayan proporcionado IDs
        if (empty($missingIds)) {
            return response()->json([
                'message' => 'No se proporcionaron IDs para procesar',
                'success' => false
            ], 400);
        }

        // Procesar los IDs en bloques
        foreach (array_chunk($missingIds, $chunkSize) as $chunk) {
            try {
                // Obtener registros completos de la base secundaria
                $records = DB::connection('mysql_secondary')
                    ->table('reservations')
                    ->whereIn('id', $chunk)
                    ->get();

                // Insertar en bloques en la base primaria
                foreach ($records as $record) {
                    try {
                        DB::connection('mysql')->table('reservations')->insert((array)$record);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = [
                            'id' => $record->id,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Registrar error en el chunk completo
                $errorCount += count($chunk);
                $errors[] = [
                    'chunk' => implode(',', $chunk),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Proceso de copia completado',
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
            'processed_ids' => $missingIds
        ], 200);
    }

    public function insertReservationItems(Request $request){
        // Ejemplo de llamada desde otro controlador o ruta
        $response = $this->validateReservationItems();
        $missingIds = json_decode($response->getContent(), true)['data'];

        $copyResponse = $this->copyMissingReservationItems(new Request(['ids' => $missingIds]));

        return response()->json($copyResponse,200);
    }

    public function getDataFromBothDatabases()
    {
        // Consulta a la base de datos primaria
        $usersFromPrimary = DB::connection('mysql')
            ->table('users')
            ->select('id', 'name', 'email')
            ->limit(5)
            ->get();
            
        // Consulta a la base de datos secundaria
        $productsFromSecondary = DB::connection('mysql_secondary')
            ->table('products')
            ->select('id', 'name', 'price')
            ->limit(5)
            ->get();
            
        return response()->json([
            'users_from_primary' => $usersFromPrimary,
            'products_from_secondary' => $productsFromSecondary
        ]);
    }
    
    // Método para consultas más complejas que unen datos de ambas DBs
    public function getCombinedData()
    {
        // Obtener datos de la primera DB
        $primaryData = DB::connection('mysql')
            ->table('some_table')
            ->where('active', 1)
            ->get();
            
        // Obtener datos de la segunda DB
        $secondaryData = DB::connection('mysql_secondary')
            ->table('related_table')
            ->whereIn('id', $primaryData->pluck('foreign_id'))
            ->get();
            
        // Combinar los datos manualmente
        $combined = $primaryData->map(function($item) use ($secondaryData) {
            $related = $secondaryData->where('id', $item->foreign_id)->first();
            return [
                'primary_id' => $item->id,
                'primary_name' => $item->name,
                'related_info' => $related ? $related->info : null
            ];
        });
            
        return response()->json($combined);
    }
}
