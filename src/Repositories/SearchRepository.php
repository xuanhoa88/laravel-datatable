<?php

namespace Llama\TableView\Repositories;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class SearchRepository 
{
	/**
	 * @var string
	 */
	protected $searchQuery;

	/**
	 * @var string
	 */
	protected $searchValues;

	/**
	 * @var array
	 */
	protected $searchFields = [];

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->searchQuery = Request::input('q', '');
		$this->searchValues = explode(' ', $this->searchQuery);
		$this->searchValues[] = $this->searchQuery;
	}

	/**
     * @param mixed $searchField
     * @return void
     */
	public function field($searchField)
	{
		if ( is_string($searchField) ) {
			$this->searchFields[] = $searchField;
		} else if ( is_array($searchField) ) {
			$this->searchFields = array_merge(
				$this->searchFields, $searchField
			);
		}
	}

	/**
     * @return boolean
     */
	public function isEnabled()
	{
		return (bool) count($this->searchFields);
	}

	/**
     * @return string
     */
	public function queryString()
	{
		return $this->searchQuery;
	}

	/**
     * Filter data collection for search query if there is one
     *
     * @param Collection $collection
     * @return Collection
     */
	public function addSearch(Collection $collection)
	{
		if ( ! $this->searchQuery ) {
			return $collection;
		}

		$searchableFields = $this->searchFields;
		return $collection->where(function($data) use ($searchableFields)
		{
			$i = 0;
			foreach ($searchableFields as $searchableProperty) {
				$whereClause = ($i === 0) ? 'where' : 'orWhere';

				if (strpos($searchableProperty, '{') !== false) {
					$searchableProperty = str_replace('{', '', $searchableProperty);
					$searchableProperty = str_replace('}', '', $searchableProperty);

					$searchableProperty = \DB::raw($searchableProperty);
				}

				$data->{$whereClause}(
					$searchableProperty, 'LIKE', "%{$this->searchQuery}%"
				);

				$i++;
			}
		});
	}
}