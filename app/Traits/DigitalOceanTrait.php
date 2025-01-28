<?php
namespace App\Traits;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReservationsMedia;

//TRAIS
use App\Traits\FollowUpTrait;

trait DigitalOceanTrait
{
    use FollowUpTrait;
    
    private function getS3Client()
    {
        return new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key'    => config('services.digital_ocean.key'),
                'secret' => config('services.digital_ocean.secret'),
            ],
        ]);
    }

    public function uploadMedia($request)
    {
        $validator = Validator::make($request->all(), [
            'folder' => 'required|string',            
            'file' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'type_media' => 'required|string|in:GENERAL,CANCELLATION,OPERATION,REFUND',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params',
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 404);
        }


        $client = $this->getS3Client();
        $file = $request->file('file');
        $filePath = $request->input('folder') . '/' . strtotime(date("Y-m-d H:i:s")). "-" .$file->getClientOriginalName();
        $mimeType = mime_content_type($file->getPathname());

        try {
            $result = $client->putObject([
                'Bucket' => config('services.digital_ocean.bucket'),
                'Key'    => $filePath,
                'SourceFile' => $file->getPathname(),
                'ContentType' => $mimeType,
                'ACL'    => 'public-read',
            ]);

            $media = new ReservationsMedia();
            $media->reservation_id = $request->input('folder');
            $media->path = $filePath;
            $media->url = $result['ObjectURL'];
            $media->type_media = $request->type_media;
            $media->save();

            $this->create_followUps($request->input('folder'), "El usuario: ".auth()->user()->name.", ha agregado el archivo multimedia: ".$file->getClientOriginalName(), 'HISTORY', 'MEDIA');
            
            if( isset($request->type_action) && $request->type_action == "upload" ){
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'data' => array(
                        "item"  => $request->id, //ITEM DE LA TABLA DE OPERACIONES
                        "reservation" => $request->folder, // EL ID DE LA RESERVACIÓN
                        "status"  => 1,
                        "message" => "Se agrego una imagen a la reserva: ".$request->folder.", por ".auth()->user()->name
                    )
                ]);
            }else{
                return response()->json(['message' => 'Image uploaded successfully', 'url' => $result['ObjectURL']]);
            }
        } catch (Aws\Exception\AwsException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteMedia($request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params',
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 404);
        }

        $media = ReservationsMedia::find( $request->id ); 
        if($media):

            $client = $this->getS3Client();
            try {
                $client->deleteObject([
                    'Bucket' => config('services.digital_ocean.bucket'),
                    'Key'    => $media->path,
                ]);
                
                $this->create_followUps($media->reservation_id, "El usuario: ".auth()->user()->name.", ha eliminado el archivo multimedia: ".$request->name, 'HISTORY', 'MEDIA');
                
                $media->delete();

                return response()->json(['message' => 'Image deleted successfully']);
            } catch (Aws\Exception\AwsException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        else:
            return response()->json(['error' => 'Media ID not found...'], 500);
        endif;
        
    }

}