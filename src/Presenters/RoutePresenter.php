<?php

namespace Llama\TableView\Presenters;

class RoutePresenter
{
	/**
     * Returns current uri with params
     *
     * @param string $currentPath
     * @param array $routeParameters
     * @return string
     */
	public static function withParam($currentPath, array $routeParameters)
	{
		return '/' 
			. $currentPath .'?'
            . http_build_query($routeParameters, null, '&');
	}
}