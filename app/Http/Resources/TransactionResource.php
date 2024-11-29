<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'transactionDate' =>  (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'transactionType' => $this->type,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'narration' => $this->narration,
        ];
    }
}
