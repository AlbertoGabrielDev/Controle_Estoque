<?php

namespace Modules\Purchases\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Purchases\Models\PurchaseDocumentSequence;

class DocumentNumberService
{
    private const ALLOWED_TYPES = [
        'REQ',
        'COT',
        'PO',
        'REC',
        'DEV',
        'AP',
    ];

    /**
     * Generate a new document number for the given type.
     *
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function generate(string $type): string
    {
        $type = strtoupper(trim($type));
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Unsupported document type: ' . $type);
        }

        return DB::transaction(function () use ($type): string {
            $sequence = PurchaseDocumentSequence::query()
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                try {
                    $sequence = PurchaseDocumentSequence::query()->create([
                        'type' => $type,
                        'last_number' => 0,
                    ]);
                } catch (QueryException $exception) {
                    $sequence = PurchaseDocumentSequence::query()
                        ->where('type', $type)
                        ->lockForUpdate()
                        ->firstOrFail();
                }
            }

            $sequence->last_number = $sequence->last_number + 1;
            $sequence->save();

            return sprintf('%s-%06d', $type, $sequence->last_number);
        });
    }
}
