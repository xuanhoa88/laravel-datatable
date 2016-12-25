<?php

namespace Llama\TableView\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Llama\TableView\CookieStorage\LookInStorage;
use Llama\TableView\CookieStorage\UpdateStorage;
use Llama\TableView\Presenters\RoutePresenter;


class TableViewCookieStorage
{
	/**
     * @var string 
     */
	protected $redirectRoute;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
    	$currentPath = $request->path();
		if ( $this->beforeMiddleware($request, $currentPath) ) {
			return redirect( $this->redirectRoute );
		}

        return $this->afterMiddleware($request, $next($request), $currentPath);
    }

    /**
     * @param Request $request
     * @param string $currentPath
     * @return boolean
     */
    protected function beforeMiddleware(Request $request, $currentPath)
    {
		$searchQuery = LookInStorage::forSearch($request, $currentPath);
    	$pageNumber = LookInStorage::forPage($request, $currentPath);
    	$perPage = LookInStorage::forLimit($request, $currentPath);

		if ( $shouldRedirect = ( (bool) $searchQuery || (bool) $pageNumber || (bool) $perPage ) ) {
			$redirectParameters = LookInStorage::forRedirectParameters($request->all(), $searchQuery, $pageNumber,  $perPage);
			$this->redirectRoute = RoutePresenter::withParam( $currentPath, $redirectParameters );
		}

		return $shouldRedirect;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $currentPath
     * @return Response
     */
    protected function afterMiddleware(Request $request, Response $response, $currentPath)
    {
    	return UpdateStorage::forever(UpdateStorage::forResponse($response, $request, $currentPath), $request, $currentPath);
    }
}
