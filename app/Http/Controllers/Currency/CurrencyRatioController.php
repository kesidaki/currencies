<?php

namespace App\Http\Controllers\Currency;

use Sanitizer;
use Illuminate\Http\Request;
use App\Eloquent\CurrencyRepository;
use App\Http\Controllers\ApiController;
use App\Eloquent\CurrencyRatioRepository;

class CurrencyRatioController extends ApiController
{
    public $currency;
    public $ratio;

    /**
    * Constructor
    */
    public function __construct(CurrencyRepository $currencyRepository, 
                                CurrencyRatioRepository $currencyRatioRepository) 
    {
        $this->currency  = $currencyRepository;
        $this->ratio     = $currencyRatioRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $ratios = $this->ratio->getLatestRatios($id);

        return $this->showAll($ratios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $baseId)
    {
        # check if base currency exists
        $baseCurrency = $this->currency->find($baseId);
        if (!$baseCurrency) {
            return $this->errorResponse('Base currency was not found', 404);
        }

        # validation
        $this->validate($request, 
            // rules
            [
                'target' => 'required|numeric|exists:currencies,id',
                'rate'   => 'required|numeric|between:0,99.99'
            ],
            // messages
            [
                'target.required' => 'Target Currency value must be an ID',
                'target.number'   => 'Target Currency value must be an Integer',
                'target.exists'   => 'Target Currency must already exist',
                'rate.required'   => 'Rate is required',
                'rate.numeric'    => 'Rate must be numeric',
                'rate.between'    => 'Rate mube be between :min and :max'
            ]
        );

        # store input data
        $data = [
            'base_currency_id'   => $baseId,
            'target_currency_id' => $request->target,
            'ratio'              => floatval($request->rate)
        ];

        # input data filtering
        $filters = [
            'base_currency_id'   => 'trim|escape|cast:integer',
            'target_currency_id' => 'trim|escape|cast:integer',
            'ratio'              => 'trim|escape|cast:float'
        ];
        $newData = Sanitizer::make($data, $filters)->sanitize();

        # Create to keep records of previous rates
        # Needs fixing because get query returns earliest entry instead of latest 
        # store given rate
        // $this->ratio->create($newData);
        # store reversed rate
        // $this->ratio->create([
        //     'base_currency_id'   => $newData['target_currency_id'],
        //     'target_currency_id' => $newData['base_currency_id'],
        //     'ratio'              => $this->reverseRate($newData['ratio'])
        // ]);

        # create or update given rate
        $this->ratio->createOrUpdateRatio($newData);
        # create or update reversed rate
        $this->ratio->createOrUpdateRatio([
            'base_currency_id'   => $newData['target_currency_id'],
            'target_currency_id' => $newData['base_currency_id'],
            'ratio'              => $this->reverseRate($newData['ratio'])
        ]);

        $ratios = $this->ratio->getLatestRatios($baseId);

        return $this->showAll($ratios);
    }

    /**
     * Display the specified resource.
     *
     * @param  int baseId
     * @param  int targetId
     * @return \Illuminate\Http\Response
     */
    public function show($baseId, $targetId)
    {
        $ratios = $this->ratio->getRatios($baseId, $targetId);

        return $this->showAll($ratios);
    }

    /**
    * Reverse currency rate
    *
    * @param float $rate
    */
    private function reverseRate($rate)
    {
        return 1 / $rate;
    }
}
