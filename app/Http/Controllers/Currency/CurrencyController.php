<?php

namespace App\Http\Controllers\Currency;

use Sanitizer;
use Illuminate\Http\Request;
use App\Eloquent\CurrencyRepository;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class CurrencyController extends ApiController
{
    public $currency;

    /**
    * Constructor
    */
    public function __construct(CurrencyRepository $currensyRepository) 
    {
        $this->currency = $currensyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->currency->getAll($request->hide);
        return $this->showAll($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        # validation
        $this->validate($request, 
            // rules
            [
                'currency'      => 'required|min:4|unique:currencies,name',
                'abbreviation'  => 'required|min:3|unique:currencies,code'
            ],
            // message
            [
                'currency.required'     => 'Currency Name is required',
                'currency.min'          => 'Currency must be at least :min characters long',
                'currency.unique'       => 'Currency name already exists',
                'abbreviation.required' => 'Currency Abbreviation is required',
                'abbreviation.min'      => 'Currency Abbreviation must be at least :min characters long',
                'abbreviation.unique'   => 'Currency Abbreviation already exists'
            ]
        );

        # data manipulation
        $data = [
            'name'   => $request->currency,
            'code'   => $request->abbreviation,
            'symbol' => $request->sign
        ];

        # input data filters
        $filters = [
            'name'   => 'trim|escape|cast:string',
            'code'   => 'trim|escape|cast:string',
            'symbol' => 'trim|escape|cast:string'
        ];

        $newData = Sanitizer::make($data, $filters)->sanitize();

        # image placeholder
        $newData['icon'] = '\icons\currency.png';

        # store data
        $currency = $this->currency->create($newData);

        return $this->showOne($currency);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $currency = $this->currency->find($id);

        return $this->showOne($currency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $currency = $this->currency->find($id);
        if (!$currency) {
            return $this->errorResponse('Currency was not found', 404);
        }

        # validation
        $this->validate($request, 
            // rules
            [
                'currency'      => 'required|min:4',
                'abbreviation'  => 'required|min:3'
            ],
            // messages
            [
                'currency.required'     => 'Currency Name is required',
                'currency.min'          => 'Currency must be at least :min characters long',
                'abbreviation.required' => 'Currency Abbreviation is required',
                'abbreviation.min'      => 'Currency Abbreviation must be at least :min characters long'
            ]
        );

        # data manipulation
        $data     = [
            'name'   => $request->currency,
            'code'   => $request->abbreviation,
            'symbol' => $request->sign
        ];

        # input data filters
        $filters = [
            'name'   => 'trim|escape|cast:string',
            'code'   => 'trim|escape|cast:string',
            'symbol' => 'trim|escape|cast:string'
        ];

        $newData = Sanitizer::make($data, $filters)->sanitize();

        # upload image
        if ($request->hasFile('image')) {
            if ($currency->icon != '') {
                Storage::delete($currency->icon);
            }

            $newData['icon'] = $request->image->store('icons');
        }

        # store data
        $this->currency->update($newData, $id);
        $currency = $this->currency->find($id);

        return $this->showMessage($currency);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = $this->currency->find($id);
        if ($currency->icon != '') {
            Storage::delete($currency->icon);
        }

        $deleted = $this->currency->delete($id);

        return $this->showMessage('Deleted '.$deleted.' row(s)');
    }
}
