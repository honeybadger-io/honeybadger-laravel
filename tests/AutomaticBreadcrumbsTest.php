<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\RouteMatched;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class AutomaticBreadcrumbsTest extends TestCase
{
    /** @test */
    public function adds_breadcrumbs_for_routes_and_views()
    {
        Config::set('honeybadger.breadcrumbs.automatic', RouteMatched::class);
        Route::namespace('Honeybadger\Tests\Fixtures')
            ->group(function () {
                Route::get('test', 'TestController@index')->name('testing');
            });

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('addBreadcrumb')
            ->with($this->equalTo('Route matched'), $this->equalTo([
                'uri' => 'test',
                'methods' => 'GET,HEAD',
                'handler' => 'TestController@index',
                'name' => 'testing',
            ]), $this->equalTo('request'));

        $this->app->instance(Reporter::class, $honeybadger);

        $this->get('test');
    }
}
