<?php

namespace App\Http\Controllers\Tpv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\TpvRequest2;
use App\Http\Requests\TpvCreateRequest2;

//PACKAGE QR
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;

//REPOSITORY
use App\Repositories\Tpv\TpvRepository2;

class TpvController2 extends Controller
{
    private $tpvRepository;

    public function __construct(TpvRepository2 $TpvRepository2){
        $this->tpvRepository = $TpvRepository2;        
    }

    public function book(Request $request){
        return view('tpv.index2', compact('request'));
    }

    public function create(Request $request){
        return $this->tpvRepository->create($request);
    }

    public function createQr($view, $id, $language){
        $picture = asset('/assets/img/logos/isotipo.png');
        $url = ( $view == 'tpv' ? ( $language == "es" ? route('tpv.book.es', [ 'locale' => 'es', 'id' => $id ]) : route('tpv.book', [ 'id' => $id ]) ) : ( config('app.env') == 'local' ? config('app.domain.local') : config('app.domain.production') ).'?id='.$id );
        $qr = QrCode::create($url);

        $logo = Logo::create($picture)
            ->setResizeToWidth(60)
            ->setPunchoutBackground(true)
        ;

        $writer = new PngWriter();
        $result = $writer->write($qr);
        header('Content-Type: '.$result->getMimeType());
        echo $result->getString();
        exit;
    }
}
