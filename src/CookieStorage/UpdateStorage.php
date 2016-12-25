<?php

namespace Llama\TableView\CookieStorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateStorage
{
	/**
     * @param Response $response
     * @param Request $request
     * @param string $currentPath
     * @return Response
     */
    public static function forResponse(Response $response, Request $request, $currentPath)
    {
    	$tableViewSearchCookie = static::findValueOrForget($request, $currentPath . '.searchQuery', 'q');
		$response = $response->withCookie( $tableViewSearchCookie );

    	$tableViewPageCookie = static::findValueOrForget($request, $currentPath . '.currentPage', 'page');
		$response = $response->withCookie( $tableViewPageCookie );

		return $response;
    }

	/**
     * @param Response $response
     * @param Request $request
     * @param string $currentPath
     * @return Response
     */
    public static function forever(Response $response, Request $request, $currentPath)
    {
		if ( $request->has('sortedBy') ) {
			$response = $response
				->withCookie( cookie()->forever( $currentPath . '.sortedBy', $request->input('sortedBy') ) )
				->withCookie( cookie()->forever( $currentPath . '.sortAscending', $request->input('asc') ) );
		}

		if ( $request->has('limit') ) {
			$response = $response
				->withCookie( cookie()->forever( $currentPath . '.perPage', $request->input('limit') ) );
		}

		return $response;
    }

	/**
     * @param Request $request
     * @param string $cookieName
     * @param string $requestKeyName
     * @return \Illuminate\Support\Facades\Cookie
     */
    protected static function findValueOrForget($request, $cookieName, $requestKeyName)
    {
		if ( ! $request->has( $requestKeyName ) )
		{
			return cookie()->forget( $cookieName );
		}

		return cookie( $cookieName, $request->input( $requestKeyName ) );
    }
}
