<?php

namespace Llama\TableView\Facades;

use Illuminate\Support\Facades\Facade;

class TableViewFacade extends Facade 
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor() 
    { 
    	return 'TableView'; 
    }

}