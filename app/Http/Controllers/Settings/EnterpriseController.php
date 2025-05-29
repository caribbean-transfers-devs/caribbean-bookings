<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\EnterpriseRequest;

//REPOSITORY
use App\Repositories\Settings\EnterpriseRepository;
use App\Repositories\Reservations\UploadRepository;

//TRAITS
use App\Traits\RoleTrait;

class EnterpriseController extends Controller
{
    use RoleTrait;
    
    private $enterprise;
    private $upload;

    public function __construct(EnterpriseRepository $EnterpriseRepository, UploadRepository $UploadRepository)
    {
        $this->enterprise = $EnterpriseRepository;
        $this->upload = $UploadRepository;
    }

    public function index(Request $request)
    {
        if($this->hasPermission(73)){
            return $this->enterprise->index($request);
        }
    }

    public function create(Request $request)
    {
        return $this->enterprise->create($request);
    }

    public function store(EnterpriseRequest $request)
    {
        return $this->enterprise->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->enterprise->edit($request, $id);
    }

    public function update(EnterpriseRequest $request, $id)
    {
        return $this->enterprise->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->enterprise->destroy($request, $id);
    }

    public function uploadMedia(Request $request){
        // if($this->hasPermission(64)){
            return $this->upload->addEnterprise($request);
        // }
    }

    public function deleteMedia(Request $request){
        // if($this->hasPermission(66)){
            return $this->upload->deleteEnterprise($request);
        // }
    }    

    public function getMedia(Request $request){
        // if($this->hasPermission(65)){
            return $this->enterprise->getMedia($request);
        // }
    }    
}
