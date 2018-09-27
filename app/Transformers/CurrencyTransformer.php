<?php

namespace App\Transformers;

use App\Models\Currency;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Currency $currency)
    {
        return [
            'id' => (int) $currency->id,
            'currency' => (string) $currency->name,
            'abbreviation' => (string) $currency->code,
            'sign' => (string) $currency->symbol,
            'img' => public_path($currency->icon)
        ];
    }
}
