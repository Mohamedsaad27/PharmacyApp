<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponseTrait;

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $senderId = Auth::id();
            $recipientId = $request->input('recipient');
            $messageText = $request->input('message');
            $imagePath = null;

            $sender = Auth::user();
            $recipient = User::find($recipientId);

            if (!$recipient){
                return $this->errorResponse('Recipient not found', 404);
            }
            if ($sender->role === 'patient' && $recipient->role === 'pharmacy') {
                $patientId = $senderId;
                $pharmacyId = $recipientId;
            } elseif ($sender->role === 'pharmacy' && $recipient->role === 'patient') {
                $patientId = $recipientId;
                $pharmacyId = $senderId;
            } else {
                return $this->errorResponse('Invalid chat participants', 400);
            }

            $chat = Chat::where('patient_id', $patientId)
                ->where('pharmacy_id', $pharmacyId)
                ->first();

            if (!$chat) {
                $chat = Chat::create([
                    'patient_id' => $patientId,
                    'pharmacy_id' => $pharmacyId,
                    'started_at' => now(),
                ]);
            }

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('chat_images', 'public');
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $senderId,
                'message' => $messageText,
                'image_path' => $imagePath,  // Save the image path
                'send_at' => now(),
            ]);

            return $this->successResponse($message, 'Message sent successfully', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function getChatWithPharmacyForUser(Request $request)
    {
        try {
            $pharmacyId = $request->input('pharmacy');
            $chat = Chat::with('messages')->where('pharmacy_id', $pharmacyId)->first();
            if (!$chat) {
                return $this->errorResponse('No chat with this Pharmacy', 404);
            }
            return $this->successResponse($chat, 'Chat Retrieved Successfully', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

    public function getChatWithPatientForPharmacy(Request $request)
    {
        try {
            $patientId = $request->input('patient');
            $chat = Chat::with('messages')->where('patient_id', $patientId)->first();
            if (!$chat) {
                return $this->errorResponse('No chat with this Patient', 404);
            }
            return $this->successResponse($chat, 'Chat Retrieved Successfully', 200);
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
}
