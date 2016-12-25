<?php

namespace Llama\TableView\Repositories;

use Illuminate\Http\Request;
use Llama\TableView\TableViewColumn;
use Illuminate\Database\Eloquent\Collection;

class SortRepository 
{
	/**
	 * @var array
	 */
	protected $defaultSort;

	/**
     * @var string
     */
	protected $sortedBy;

	/**
     * @var boolean
     */
	protected $sortAscending;

	/**
     * @param TableViewColumn $column
     * @return void
     */
	public function setDefault(TableViewColumn $column)
	{
		$this->instance(
			$column->propertyName(), 
			$column->isSortAscending()
		);
	}

	/**
     * @param string $propertyName
     * @param boolean $isAscending
     * @return void
     */
	public function setDefaultFromCookie($propertyName, $isAscending)
	{
		$this->instance($propertyName, $isAscending);
	}

	/**
     * @return string
     */
	public function sortedBy()
	{
		return $this->sortedBy;
	}

	/**
     * @return string
     */
	public function sortAscending()
	{
		return $this->sortAscending;
	}

	/**
     * @param Collection $collection
     * @param array $columns
     * @return Collection
     */
	public function addOrder(Collection $collection, array $columns)
	{
		if ( ! isset($this->defaultSort['property']) ) {
			$this->defaultSort = $this->find($columns);
		}

		$this->sortedBy = Request::input('sortedBy', $this->defaultSort['property']);
		$this->sortAscending = Request::input('asc', $this->defaultSort['isAscending']);

		if ( ! $this->sortedBy) {
			return $collection;
		}

		$sortField = $this->sortedBy;
		if (strpos($sortField, '{') !== false) {
			$sortField = str_replace('{', '', $sortField);
			$sortField = str_replace('}', '', $sortField);
			$sortField = \DB::raw($sortField);
		}

		return $collection->orderBy( $sortField, $this->sortAscending ? 'ASC' : 'DESC');
	}

	/**
     * @param string $propertyName
     * @param boolean $isAscending
     * @return void
     */
	protected function instance($propertyName, $isAscending)
	{
		$this->defaultSort = [
			'property' 	  => $propertyName,
			'isAscending' => $isAscending
		];
	}

	/**
     * @param array $columns
     * @return array
     */
	protected function find(array $columns)
	{
		foreach($columns as $column)
		{
			if ( $column->isSortable() )
			{
				return [
					'property' 	  => $column->propertyName(),
					'isAscending' => true
				];
			}
		}
	}
}