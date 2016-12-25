<?php

namespace Llama\TableView\Presenters;

use Llama\TableView\Presenters\RoutePresenter;

use Illuminate\Http\Request;

class PerPageDropdownPresenter
{
	/**
     * Options for table view row count
     *
     * @var array
     */
	protected static $pageLimitOptions = [10, 25, 50, 100];

	/**
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param int $limit
     * @return string
     */
	public static function pageLimitOptions($limit)
	{
		$currentLimit = (int) Request::input('limit', 10);
		$totalOptions = count( static::$pageLimitOptions );

		$htmlSelectOptions = [];
		for ( $i = 0; $i < $totalOptions; $i++ ) {
			$pageLimit = static::$pageLimitOptions[$i];
			if ( 
				$pageLimit <= $limit 
				|| $pageLimit <= $currentLimit
				|| ( $i >= 1 && static::$pageLimitOptions[$i-1] < $limit ) 
			) {
				$htmlSelectOptions[] = $pageLimit;
			}
		}

		return $htmlSelectOptions;
	}

	/**
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param string $currentPath
     * @param int $limit
     * @return string
     */
	public static function optionTag($currentPath, $limit)
	{
		$htmlTag = '<option value="' . RoutePresenter::withParam($currentPath, array_merge([
				'page'  => 1,
				'limit' => $limit
			], Request::except('page', 'limit') 
		)) . '" ';

		$currentLimit = (int) Request::input('limit', 10);
		if ( $limit === $currentLimit ) {
			$htmlTag .= 'selected ';
		}

		return $htmlTag . '>' .  $limit . '</option>';
	}
}