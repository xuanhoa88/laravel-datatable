<?php 

namespace Llama\TableView;

use Illuminate\Support\ServiceProvider;
use Llama\TableView\TableView;

class TableViewServiceProvider extends ServiceProvider 
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    	$this->loadViewsFrom(__DIR__ . '/../views', 'table-view');

    	$this->publishes([
	        __DIR__ . '/../views' => base_path('resources/views/vendor/table-view'),
	    ]);

	    $this->publishes([
	        __DIR__ . '/../assets' => public_path('vendor/table-view'),
	    ], 'public');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['TableView'] = $this->app->share(function($app)
        {
            return new TableView();
        });
    }

}