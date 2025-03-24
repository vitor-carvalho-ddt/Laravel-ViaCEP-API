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
        try
        {
            $details = [
                'email' => $data['email'],
                'title' => $data['title'],
                'message' => $data['message'],
            ];

            SendEmailJob::dispatch($details);

            Log::info('Email queued successfully', [
                'to' => $data['email'],
                'subject' => $data['title'],
                'user_id' => auth()->id() ?? 'guest'
            ]);

            return true;
        }catch (\Exception $e){
            Log::error('Failed to queue email', [
                'to' => $data['email'] ?? 'unknown',
                'subject' => $data['title'] ?? 'unknown',
                'error' => $e->getMessage(),
                'user_id' => auth()->id() ?? 'guest'
            ]);

            return false;
        }
    }
}
