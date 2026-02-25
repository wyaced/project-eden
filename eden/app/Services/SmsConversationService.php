<?php

namespace App\Services;

use App\Services\ProduceService;

class SmsConversationService
{
    public function deleteListingConversation(String $userResponse, array $data)
    {
        $produceService = app(ProduceService::class);
        if ($userResponse === 'yes') {
            $produceService->deleteListing($data['listing_id']);
            $message = "ListingId {$data['listing_id']} deleted successfully.";
        } elseif ($userResponse === 'no') {
            $message = "Deletion of ListingID {$data['listing_id']} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }
}