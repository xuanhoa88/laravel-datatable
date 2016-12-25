<?php

namespace Llama\TableView\Presenters;

use Llama\TableView\TableView;
use Llama\TableView\Presenters\TableViewTitlePresenter;
use Llama\TableView\Presenters\PerPageDropdownPresenter;
use Llama\TableView\Presenters\SortArrowsPresenter;

class TableViewPresenter
{
	/**
     * @var TableView
     */
	protected $tableView;

	/**
     * @param TableView $tableView
     */
	public function __construct(TableView $tableView)
	{
		$this->tableView = $tableView;
	}

	/**
     * Returns title for tableview panel header
     *
     * @return string
     */
	public function title()
	{
		return TableViewTitlePresenter::formattedTitle( $this->tableView, $this->tableView->size() );
	}

	/**
     * Returns view file name for given dataset: empty or filled
     *
     * @return string
     */
	public function table($viewPartial = 'table-view::partials.')
	{
		return $viewPartial . ($this->tableView->size() ? '_filled' : '_empty');
	}

	/**
     * Per Page Dropdown 
     * Returns Options for table view row count
     *
     * @return array
     */
	public function perPageOptions()
	{
		return PerPageDropdownPresenter::pageLimitOptions( 
			$this->tableView->size() 
		);
	}

	/**
     * Per Page Dropdown 
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param int $limit
     * @return string
     */
	public function perPageOptionTagFor( $limit )
	{
		return PerPageDropdownPresenter::optionTag(
			$this->tableView->currentPath(),
			$limit
		);
	}

	/**
     * Sort Arrows Button
     * Returns current uri with params for sorting by the specified property
     *
     * @param string $columnName
     * @return string
     */
	public function sortArrowAnchorTagLinkForColumnWithName($columnName)
	{
		return SortArrowsPresenter::anchorTagLink(
			$this->tableView->currentPath(),
			$this->tableView->sortedBy(), 
			$this->tableView->sortAscending(), 
			$columnName
		);
	}

	/**
     * Sort Arrows Button
     * Returns font awesome icon class name : fa-sort,fa-sort-asc,fa-sort-desc
     *
     * @param string $columnName
     * @return string
     */
	public function sortArrowIconClassForColumnWithName($columnName)
	{
		return SortArrowsPresenter::iconClassName(
			$this->tableView->sortedBy(), 
			$this->tableView->sortAscending(), 
			$columnName
		);
	}
}