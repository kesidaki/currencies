<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\CurrencyRatioTransformer;

class CurrencyRatio extends Model
{
	public $transformer = CurrencyRatioTransformer::class;
    protected $fillable = ['base_currency_id', 'target_currency_id', 'ratio'];
}
