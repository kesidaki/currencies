<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\CurrencyTransformer;

class Currency extends Model
{
	public $transformer = CurrencyTransformer::class;
    protected $fillable = ['icon', 'name', 'code', 'symbol'];
}
