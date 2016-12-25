<?php

namespace Llama\TableView;

use Illuminate\Database\Eloquent\Model;

class TableViewColumn
{
	/**
     * @var string
     */
	protected $label;

	/**
     * @var string
     */
	protected $propertyName;

	/**
     * @var Closure
     */
	protected $customValue;

	/**
     * @var boolean
     */
	protected $sortable;

	/**
     * @var boolean
     */
	protected $sortDefault;

	/**
     * @var boolean
     */
	protected $searchable;
	
	/**
	 * @var boolean
	 */
	protected $defaultSortAscending;

	/**
     * Build the column
     *
     * @param mixed $label
     * @param mixed $value
     */
	public function __construct($label, $value)
	{
		$this->sortable = false;
		$this->sortDefault = false;
		$this->searchable = false;

		if ( is_null($value) ) {
			$value = $label;
			$label = '';
		}

		$this->label = ( $label === false ) ? '' : $label;

		$this->debunk($value);
	}

	/**
     * Get the title for the column header
     *
     * @return string
     */
	public function label()
	{
		return $this->label;
	}

	/**
     * Get the property name for the corresponding column and data model->property
     *
     * @return string
     */
	public function propertyName()
	{
		return $this->propertyName;
	}

	/**
     * Get the value for a specific row in the table
     *
     * @param Model $model
     * @return string
     */
	public function rowValue(Model $model)
	{
		if ( ! isset($this->customValue) ) {
			return $model->{$this->propertyName};
		}
		
		$closure = $this->customValue;
		return $closure($model);
	}

	/**
     * Get whether or not the column is sortable by its property
     *
     * @return boolean
     */
	public function isSortable()
	{
		return $this->sortable;
	}

	/**
     * Get whether or not the column contains the default property for sorting
     *
     * @return mixed
     */
	public function isDefaultSort()
	{
		return $this->sortDefault;
	}

	/**
     * Get whether or not the column containing the default property for sorting is ascending
     *
     * @return boolean
     */
	public function isSortAscending()
	{
		return $this->defaultSortAscending;
	}

	/**
     * Get whether or not the column is searchable by its property
     *
     * @return boolean
     */
	public function isSearchable()
	{
		return $this->searchable;
	}

	/**
     * Get various column attributes from the input value
     *
     * @param mixed $value
     * @return void
     */
	public function debunk($value)
	{
		if ( is_string($value) ) {
			$this->parse($value);
		} elseif ( is_array($value) ) {
			foreach ($value as $property => $columnValue) {
				$this->parse($property);
				$this->customValue = $columnValue;
			}
		} else {
			$this->customValue = $value;
		}
	}

	/**
     * Get the property name and options from the input value
     *
     * @param string $value
     * @return void
     */
	protected function parse($value)
	{
		$optionsStart = explode(':', $value, 2);
		$this->propertyName = $optionsStart[0];

		$options = explode(',', isset($optionsStart[1]) ? $optionsStart[1] : '');
		foreach ($options as $option) {
			switch ($option) {
				case 'sort': 
					$this->sortable = true; 
					break;
				case 'sort*':
				case 'sort*:asc': 
					$this->sortable = true; 
					$this->sortDefault = true; 
					$this->defaultSortAscending = true; 
					break;
				case 'sort*:desc': 
					$this->sortable = true; 
					$this->sortDefault = true; 
					$this->defaultSortAscending = false; 
					break;
				case 'search': 
					$this->searchable = true; 
					break;
			}
		}
	}
}