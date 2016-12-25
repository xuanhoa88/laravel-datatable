<?php

namespace Llama\TableView\CookieStorage;

use Illuminate\Http\Request;

class LookInStorage
{
	/**
     * @param array $input
     * @param mixed $searchQuery
     * @param int $pageNumber
     * @param int $perPage
     * @return array
     */
    public static function forRedirectParameters(array $input, $searchQuery, $pageNumber, $perPage)
    {
		if ( $searchQuery ) {
			$input['q'] = $searchQuery;
		}

		if ( $pageNumber ) {
			$input['page'] = (int) $pageNumber;
		}

		if ( $perPage ) {
			$input['limit'] = (int) $perPage;
		}

		return $input;
    }

	/**
     * @param Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forSearch(Request $request, $currentPath)
    {
    	if ( ! $request->has('q')  && $request->input('q') !== ''  && $request->cookie($currentPath . '_searchQuery') ) {
			return $request->cookie($currentPath . '_searchQuery');
		}

		return false;
    }

	/**
     * @param Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forPage(Request $request, $currentPath)
    {
		if ( ! $request->has('page') && $request->input('page') !== '0' && $request->cookie($currentPath . '_currentPage') ) {
			return $request->cookie($currentPath . '_currentPage');
		}

		return false;
    }

	/**
     * @param Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forLimit(Request $request, $currentPath)
    {
		if ( ! $request->has('limit') && $request->cookie($currentPath . '_perPage') ) {
			return $request->cookie($currentPath . '_perPage');
		}

		return false;
    }
}
