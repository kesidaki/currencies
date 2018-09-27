<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Eloquent\CurrencyRepository;
use App\Http\Controllers\ApiController;
use App\Eloquent\CurrencyRatioRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConverterController extends ApiController
{
	public $currency;
	public $ratio;

	/**
	* Constructor
	*/
	public function __construct(CurrencyRepository $currencyRepository, CurrencyRatioRepository $currencyRatioRepository) 
	{
		$this->currency = $currencyRepository;
		$this->ratio    = $currencyRatioRepository;
	}

    /**
    * Convert a value from a given to a target currency
    *
    * @param $request
    *
    * @return array containing the converted value and it's sign or abbreviation
    */
    public function index(Request $request)
    {
        # Create validation routine
        $this->validate($request,
            // rules
            [
                'base'   => 'required|numeric|exists:currencies,id',
                'target' => 'required|numeric|exists:currencies,id',
                'value'  => 'required|numeric'
            ],
            // message
            [
                'base.required'   => 'Base value is required',
                'base.numeric'    => 'Please select a valid base currency',
                'base.exists'     => 'Base value must exist on Currencies',
                'target.required' => 'Target value is required',
                'target.numeric'  => 'Please select a valid target currency',
                'target.exists'   => 'Target value must exist on Currencies',
                'value.required'  => 'Value is required',
                'value.numeric'   => 'Value must be a number'
            ]
        );

        # get currencies from respective id's
        $baseCurrency   = $this->currency->find($request->base);
        $targetCurrency = $this->currency->find($request->target);

        # get ratio and update date
        if ($request->base == $request->target) {
            $rate        = 1;
            $updatedAt   = date('d/m/y');
        }
        else {
            $latestRatio = $this->ratio->getLatestRatio($request->base, $request->target);

            # if ratio does not exist, send 404
            if (!$latestRatio) {
                return $this->errorResponse('Rate not found', 404);
            }

            $rate        = $latestRatio->ratio;
            $updatedAt   = date('d/m/y', strtotime($latestRatio->created_at));
        }

        # get convert value
        $convertedValue = $request->value * $rate;

        # build response
        return $this->showMessage([
            'base' => [
                'value' => $request->value,
                'sign'  => ($baseCurrency->symbol != '') ? $baseCurrency->symbol : $baseCurrency->code,
            ],
        	'target' => [
                'value' => $convertedValue,
                'sign'  => ($targetCurrency->symbol != '') ? $targetCurrency->symbol : $targetCurrency->code
            ],
            'rate'  => $rate,
            'lastUpdated' => $updatedAt
        ]);
    }
}
