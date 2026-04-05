<?php

namespace Modules\Commercial\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Commercial\Models\CommercialDocumentSequence;

class CommercialDocumentNumberService
{
    private const ALLOWED_TYPES = ['OPP', 'PROP', 'SO', 'INV', 'RET', 'AR'];

    /**
     * Generate a new sequential document number for the given type.
     *
     * The generation is wrapped in a transaction with a row-level lock to ensure
     * that concurrent requests never produce the same number.
     *
     * @param string $type  One of: OPP, PROP, SO, INV, RET, AR
     * @return string  e.g. "SO-000042"
     * @throws \InvalidArgumentException  If the type is not in the allowed list.
     * @throws \Throwable
     */
    public function generate(string $type): string
    {
        $type = strtoupper(trim($type));

        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Unsupported document type: ' . $type);
        }

        return DB::transaction(function () use ($type): string {
            $sequence = CommercialDocumentSequence::query()
                ->where('type', $type)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                try {
                    $sequence = CommercialDocumentSequence::query()->create([
                        'type' => $type,
                        'last_number' => 0,
                    ]);
                } catch (QueryException) {
                    $sequence = CommercialDocumentSequence::query()
                        ->where('type', $type)
                        ->lockForUpdate()
                        ->firstOrFail();
                }
            }

            $sequence->last_number += 1;
            $sequence->save();

            return sprintf('%s-%06d', $type, $sequence->last_number);
        });
    }
}
