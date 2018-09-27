<?php
namespace App\Eloquent;

use DB;
use App\Models\CurrencyRatio;
use App\Eloquent\Repository;
use App\Interfaces\RepositoryInterface;

class CurrencyRatioRepository extends Repository 
{ 

	function model() 
	{ 
		return 'App\Models\CurrencyRatio';
	}

	/**
	* Get (Latest) Ratios for a currency
	*
	* @param int id
	*/
	public function getLatestRatios($id)
	{
		return CurrencyRatio::select('cr1.*', DB::raw('MAX(cr1.id) as max'), 'currencies.name', 'currencies.code')
							->from('currency_ratios as cr1')
							->join('currencies', 'currencies.id', '=', 'cr1.target_currency_id')
	    					->where('cr1.base_currency_id', '=', $id)
	    					->groupBy(DB::raw('cr1.target_currency_id DESC'))
	    					->orderBy('cr1.target_currency_id', 'asc')
	    					->get();
	}

	/**
	* Get all ratios for a combination of currencies
	*
	* @param int b_id
	* @param int t_id
	*/
	public function getRatios($b_id, $t_id)
	{
	    return CurrencyRatio::select('currency_ratios.*', 'currencies.name', 'currencies.code')
							->join('currencies', 'currencies.id', '=', 'currency_ratios.target_currency_id')
							->where('base_currency_id', $b_id)
	    					->where('target_currency_id', $t_id)
	    					->orderBy('id', 'desc')
	    					->get();
	}

	/**
	* Update ratio for a combination of currencies
	* @param baseId
	* @param targetId
	*/
	public function updateRatio($baseId, $targetId, $ratio)
	{
	    return CurrencyRatio::select('currency_ratios.*', 'currencies.name', 'currencies.code')
							->join('currencies', 'currencies.id', '=', 'currency_ratios.target_currency_id')
							->where('base_currency_id', '=', $baseId)
	    					->where('target_currency_id', '=', $targetId)
	    					->update(['ratio' => $ratio]);
	}

	/**
	* Create or Update Ratio
	*
	* @param $data array containing:
	* 		@param $base_currency_id
	* 		@param $target_currency_id
	* 		@param $ratio
	*/
	public function createOrUpdateRatio($data)
	{
	    return CurrencyRatio::updateOrCreate(
	    	// criteria
	    	[
	    		'base_currency_id'   => $data['base_currency_id'],
	    		'target_currency_id' => $data['target_currency_id']
	    	],
	    	// other data
	    	[
	    		'ratio' => $data['ratio']
	    	]
	    );
	}

	/**
	* Get Latest Ratio
	*
	* @param 
	*/
	public function getLatestRatio($base, $target)
	{
	    return CurrencyRatio::select('currency_ratios.*', 'currencies.name', 'currencies.code')
							->join('currencies', 'currencies.id', '=', 'currency_ratios.target_currency_id')
							->where('base_currency_id', $base)
	    					->where('target_currency_id', $target)
	    					->orderBy('id', 'desc')
	    					->first();
	}

}

?>