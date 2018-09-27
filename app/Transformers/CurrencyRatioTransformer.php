<?php

namespace App\Transformers;

use App\Models\CurrencyRatio;
use League\Fractal\TransformerAbstract;

class CurrencyRatioTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CurrencyRatio $ratio)
    {
        return [
            'id' => $ratio->id,
            'base' => $ratio->base_currency_id,
            'target' => $ratio->target_currency_id,
            'currency' => $ratio->name,
            'abbreviation' => $ratio->code,
            'rate' => $ratio->ratio,
            'date' => date('d/m/y', strtotime($ratio->created_at))
        ];
    }
}
