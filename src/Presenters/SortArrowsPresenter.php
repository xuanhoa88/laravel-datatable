<?php

namespace Llama\TableView\Presenters;

use Llama\TableView\Presenters\RoutePresenter;
use Illuminate\Http\Request;

class SortArrowsPresenter
{
	/**
     * Returns current uri with params for sorting by the specified property
     *
     * @param string $currentPath
     * @param string $sortFieldName
     * @param boolean $sortIsAscending
     * @param string $columnName
     * @return string
     */
	public static function anchorTagLink($currentPath, $sortFieldName, $sortIsAscending, $columnName)
	{
		return RoutePresenter::withParam(($sortFieldName === $columnName ? ! $sortIsAscending : false), array_merge([
				'sortedBy' => $columnName,
				'asc' 	   => $linkSortsAscending
			], Request::except('sortedBy', 'asc') 
		));
	}

	/**
     * Returns font awesome icon class name : fa-sort,fa-sort-asc,fa-sort-desc
     *
     * @param string $sortFieldName
     * @param boolean $sortIsAscending
     * @param string $columnName
     * @return string
     */
	public static function iconClassName($sortFieldName, $sortIsAscending, $columnName)
	{
		$className = 'fa fa-sort';
		if ( $sortFieldName === $columnName ) {
			$directionName = $sortIsAscending ? 'asc' : 'desc';
			$className .= '-' . $directionName;
		}

		return $className;
	}
}