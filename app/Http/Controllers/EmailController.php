<?php
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $details = [
            'email' => $request->email,
            'title' => $request->title,
            'message' => $request->message,
        ];

        SendEmailJob::dispatch($details);

        return response()->json(['message' => 'Email sent successfully!']);
    }
}
