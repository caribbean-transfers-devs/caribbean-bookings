<?php

namespace App\Http\Controllers\Reservations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reservations\ServiceOrderRepository;
use App\Traits\RoleTrait;

class ServiceOrderController extends Controller
{
    use RoleTrait;

    private $ServiceOrderRepository;

    public function __construct(ServiceOrderRepository $ServiceOrderRepository)
    {
        $this->ServiceOrderRepository = $ServiceOrderRepository;
    }

    public function createPDF(Request $request)
    {
        // TODO: reemplazar con permiso definitivo
        if (!$this->hasPermission(6)) {
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $this->ServiceOrderRepository->createPDF($request);
    }
}
