<?php

namespace Llama\TableView\Presenters;

use Illuminate\Http\Request;
use Llama\TableView\TableView;

class TableViewTitlePresenter
{
	/**
     * @param TableView $tableView
     * @param int $limit
     * @return string
     */
	public static function formattedTitle( TableView $tableView, $limit )
	{
		$modelName = $tableView->getId();
		if ( $limit !== 1 ) {
			$modelName = str_plural( $modelName );
		}

		return static::titleWithTableFilters( $modelName, $limit );
	}

	/**
     * @param string $modelName
     * @param int $limit
     * @return string
     */
	protected static function titleWithTableFilters( $modelName, $limit )
	{
		$title = $limit > 0 ? number_format($limit) : 'No';
		if ( ! Request::has('q') ) {
			return $title . ' Total ' . $modelName;
		}

		return $title . ' ' . $modelName . ' found by searching ' . Request::get('q');
	}
}