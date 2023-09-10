<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MailJetCURL
{
    //properties to save auth data but is always the same so we can hardcode it 
    private $api_key = env('MJ_APIKEY');
    private $api_secret = env('MJ_SECRET'); 

    public function sendMail($subject,$text,$to){       

        $response = Http::withBasicAuth($this->api_key, $this->api_secret)
            ->withOptions(['verify' => false])
            ->post('https://api.mailjet.com/v3.1/send', [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => 'no-reply@caribbeantransfers.com.mx',
                            'Name' => 'Caribbean Transfers',
                        ],
                        'To' => [
                            [
                                'Email' => $to['email'],
                                'Name' => $to['name'],
                            ]
                        ],
                        'Subject' => $subject,
                        'TextPart' => $text,
                        'HTMLPart' => $text,
                    ],
                ],
            ]);

        // Check the response
        if ($response->successful()) {
            $responseData = $response->json();
            error_log('EMAIL SENT');
        } else {
            $errorMessage = $response->json();
            error_log('EMAIL SENT FAILED');
        }

    }

    public function sendMailwAttachments($subject,$to,$text,$attachments,$attach_type,$attach_name){       

        $response = Http::withBasicAuth($this->api_key, $this->api_secret)
            ->withOptions(['verify' => false])
            ->post('https://api.mailjet.com/v3.1/send', [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => 'no-reply@caribbeantransfers.com.mx',
                            'Name' => 'Caribbean Transfers',
                        ],
                        'To' => [
                            [
                                'Email' => $to['email'],
                                'Name' => $to['name'],
                            ]
                        ],
                        'Subject' => $subject,
                        'TextPart' => $text,
                        'HTMLPart' => "<h3>".$text."</h3>",
                        'Attachments' => [
                            [
                                'ContentType' => $attach_type,
                                'Filename' => $attach_name,
                                'Base64Content' => base64_encode(file_get_contents($attachments)),
                            ],
                        ],
                    ],
                ],
            ]);

        // Check the response
        if ($response->successful()) {
            $responseData = $response->json();
            error_log('EMAIL SENT');
        } else {
            $errorMessage = $response->json();
            error_log('EMAIL SENT FAILED');
        }

    }
}