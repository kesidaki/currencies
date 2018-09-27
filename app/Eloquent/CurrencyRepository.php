<?php
namespace App\Eloquent;

use DB;
use App\Models\Currency;
use App\Eloquent\Repository;
use App\Interfaces\RepositoryInterface;

class CurrencyRepository extends Repository 
{ 

	function model() 
	{ 
		return 'App\Models\Currency';
	}

	/**
	* Get All currencies, filter one if needed
	*/
	function getAll($hide='')
	{
		if ($hide != '') {
			return Currency::where('id', '!=', $hide)->orderBy('name', 'asc')->get();
		}

		return Currency::orderBy('name', 'asc')->get();
	} 

}

?>