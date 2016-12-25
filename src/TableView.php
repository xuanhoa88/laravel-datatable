<?php

namespace Llama\TableView;

use Llama\TableView\TableViewColumn;
use Llama\TableView\Presenters\TableViewPresenter;
use Illuminate\Database\Eloquent\Collection;
use Llama\TableView\Repositories\SearchRepository;
use Llama\TableView\Repositories\SortRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class TableView 
{
	/**
     * @var Collection
     */
	protected $collection;

	/**
     * @var int
     */
	protected $size;

	/**
     * @var array
     */
	protected $columns;

	/**
     * @var SortRepository
     */
	protected $sort;

	/**
     * @var SearchRepository
     */
	protected $search;

	/**
	 * @var int
	 */
	protected $perPage;

	/**
     * @var string
     */
	protected $path;

	/**
     * @var string
     */
	protected $tableName;

	/**
	 * @return void
	 */
	public function __construct()
	{
		// reference to current route
		$this->path = ltrim( Request::path(), '/');

		// sorting
		$this->sort = new SortRepository();

		// pagination
		$this->perPage = $this->limitPerPage( $this->path );

		// search
		$this->search = new SearchRepository();
	}

	/**
     * Create a new table instance with Eloquent\Collection data and column mapping
     *
     * @param mixed $collection - Illuminate\Database\Eloquent\Builder or (string) Eloquent Model Class Name
     * @param string $tableName
     * @return TableView
     */
	public static function collection($collection, $tableName = null)
	{
		if ( is_string($collection) ) {
			$collection = new $collection();
		}

		$dataTable = new static;
		$dataTable->tableName = $tableName ?  : class_basename( $collection->getModel() );
		$dataTable->collection = $collection;
		$dataTable->columns = [];

		return $dataTable;
	}

	/**
     * Add additonal search fields
     *
     * @param array $searchFields
     * @return TableView
     */
	public function search($searchFields)
	{
		$this->search->field( $searchFields );

		return $this;
	}
	
	/**
	 * Assigns columns to display.
	 *
	 * @param array $columns
	 * @return TableView
	 */
	public function columns(array $columns = [])
	{
		$this->columns = [];
		foreach ($columns as $column) {
			if (is_array($var)) {
				call_user_func_array([$this, 'column'], $column);
			} else {
				call_user_func([$this, 'column'], $column);
			}
		}

		return $this;
	}

	/**
     * Add a column to the table
     *
     * @param mixed $title
     * @param mixed $value
     * @return TableView
     */
	public function addColumn($title, $value = null)
	{
		$newColumn = new TableViewColumn($title, $value);

		$this->columns[$newColumn->propertyName()] = $newColumn;

		if ( $newColumn->isSearchable() ) {
			$this->search->field( $newColumn->propertyName() );
		}
		
		if ( $newColumn->isDefaultSort() ) {
			$this->sort->setDefault($newColumn);
		}

		return $this;
	}

	/**
     * @return string
     */
	public function getId()
	{
		return $this->tableName;
	}

	/**
     * @return Collection
     */
	public function data()
	{
		return $this->collection;
	}

	/**
     * @return int
     */
	public function size()
	{
		return $this->size;
	}

	/**
     * @return array
     */
	public function columns()
	{
		return $this->columns;
	}

	/**
     * @return boolean
     */
	public function searchEnabled()
	{
		return $this->search->isEnabled();
	}

	/**
     * @return string
     */
	public function sortedBy()
	{
		return $this->sort->sortedBy();
	}

	/**
     * @return boolean
     */
	public function sortAscending()
	{
		return $this->sort->sortAscending();
	}

	/**
     * @return string
     */
	public function currentPath()
	{
		return $this->path;
	}

	/**
     * Paginate and build tableview for view
     *
     * @return TableView
     */
	public function build()
	{
		$this->collection = $this->filteredAndSorted(
			$this->path,
			$this->collection, 
			$this->search, 
			$this->sort, 
			$this->columns 
		)->paginate( $this->perPage );

		$this->size = $this->collection->total();

		return $this;
	}

	/**
     * Return helper class for subviews
     *
     * @return TableViewPresenter
     */
	public function present()
	{
		return new TableViewPresenter($this);
	}

	/**
     * Filter collection by search query and order collection
     *
     * @param string $path
     * @param Collection $collection
     * @param SearchRepository $search
     * @return SortRepository $sort
     * @return array $columns
     * @return Collection
     */
	protected function filteredAndSorted( $path, Collection $collection, SearchRepository $search, SortRepository $sort, array $columns )
	{
		$collection = $search->addSearch($collection);
		if ( Cookie::has($path . '_sortedBy') ) {
			$sort->setDefaultFromCookie( 
				Cookie::get($path . '_sortedBy'),
				Cookie::get($path . '_sortAscending') 
			);
		}

		return $sort->addOrder($collection, $columns);
	}

	/**
     * @param string $path
     * @return int
     */
	protected function limitPerPage( $path )
	{
		if ( Request::has('limit') ) {
			$perPage = Request::input('limit');
		} elseif ( Cookie::has($path . '_perPage') ) {
			$perPage = Cookie::get($path . '_perPage');
		} else {
			$perPage = 10;
		}

		return $perPage;
	}

	/**
	 * Modifications to column.
	 *
	 * @param string  $column
	 * @param Closure $closure
	 * @return TableView
	 */
	public function modifyColumn($column, $closure)
	{
		if (array_key_exists($column, $this->columns)) {
			$this->columns[$column]->debunk($closure);
		}

		return $this;
	}

	/**
	 * Remove to column.
	 *
	 * @param string  $column
	 * @return TableView
	 */
	public function removeColumn()
	{
		foreach (func_get_args() as $column) {
			if (array_key_exists($column, $this->columns)) {
				unset($this->columns[$column]);
			}
		}

		return $this;
	}

}


