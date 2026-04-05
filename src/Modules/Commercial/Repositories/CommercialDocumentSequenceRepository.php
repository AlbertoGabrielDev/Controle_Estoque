<?php

namespace Modules\Commercial\Repositories;

use Modules\Commercial\Models\CommercialDocumentSequence;

interface CommercialDocumentSequenceRepository
{
    /**
     * Get and lock the sequence row for the given type, for safe number generation.
     *
     * @param string $type  e.g. 'OPP', 'PROP', 'SO', 'INV', 'RET', 'AR'
     * @return CommercialDocumentSequence
     */
    public function lockForUpdate(string $type): CommercialDocumentSequence;
}
