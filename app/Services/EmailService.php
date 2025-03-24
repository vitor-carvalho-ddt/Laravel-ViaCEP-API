<?php
namespace App\Services;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\CepRepositoryInterface;
// Email Job
use App\Jobs\SendEmailJob;

class EmailService
{
    public static function sendEmail(array $data)
    {
        $details = [
            'email' => $data['email'],
            'title' => $data['title'],
            'message' => $data['message'],
        ];

        SendEmailJob::dispatch($details);

        Log::info(message: 'Email sent successfully!', context: ['cep' => $data['cep'], 'user_id' => auth()->id()]);
    }
}
