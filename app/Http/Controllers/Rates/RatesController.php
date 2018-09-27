<?php

namespace App\Http\Controllers\Rates;

use Sanitizer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Eloquent\CurrencyRatioRepository;

class RatesController extends ApiController
{
    public $ratio;

    /**
    * Constructor
    */
    public function __construct(CurrencyRatioRepository $currencyRatioRepository) 
    {
        $this->ratio = $currencyRatioRepository;
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
        # get rate and check if it exists
        $ratio = $this->ratios->find($id);
        if (!$ratio) {
            return $this->errorResponse('Rate was not found', 404);
        }

        # validation
        $this->validate($request, 
            // rules
            [
                'rate'  => 'required|numeric|between:0,99.99'
            ],
            // messages
            [
                'rate.required' => 'Rate is required',
                'rate.number'   => 'Rate must be a number',
                'rate.between'  => 'Rate must be between :min and :max'
            ]
        );

        # store input data
        $data = ['ratio' => $request->rate];

        # input data filtering
        $filters = ['ratio' => 'trim|escape|cast:float'];
        $newData = Sanitizer::make($data, $filters)->sanitize();

        # update entry
        $this->ratio->update($newData, $id);
        $ratio = $this->ratios->find($id);

        return $this->showOne($ratio);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->ratio->delete($id);

        return $this->showMessage('Deleted '.$deleted.' row(s)');
    }
}
