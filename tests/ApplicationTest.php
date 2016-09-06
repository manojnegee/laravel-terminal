<?php

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\Terminal\Application;
use Recca0120\Terminal\Console\Commands\Artisan;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_call()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);
        $command = m::mock(new Artisan());

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events->shouldReceive('fire')->once();

        $app
            ->shouldReceive('offsetGet')->with('request')->once()->andReturn(null)
            ->shouldReceive('offsetGet')->with('events')->once()->andReturn(null)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__)
            ->shouldReceive('call');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Application($app, $events, 'testing');
        $application->resolveCommands([]);
        $application->add($command);
        $application->call('artisan');
    }

    public function test_artisan_string()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);
        $command = m::mock(new Artisan());

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn(ArtisanStarting::class);

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__)
            ->shouldReceive('call');

        $request->shouldReceive('ajax')->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Application($app, $events, 'testing');
        $application->resolveCommands([]);
        $application->add($command);
        $application->call('artisan');
    }

    /**
     * @expectedException Exception
     */
    public function test_exception()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);
        $input = m::mock(InputInterface::class);
        $output = m::mock(OutputInterface::class);
        $exception = m::mock(Exception::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn(ArtisanStarting::class);

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            // ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        $request->shouldReceive('ajax')->andReturn(false);

        $output->shouldReceive('writeln')->andReturnUsing(function () use ($exception) {
            throw $exception;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Application($app, $events, 'testing');
        $application->run($input, $output);
    }

    /**
     * @expectedException Exception
     */
    public function test_exception_with_ajax()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);
        $input = m::mock(InputInterface::class);
        $output = m::mock(OutputInterface::class);
        $exception = m::mock(Exception::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('fire')->once()
            ->shouldReceive('firing')->andReturn(ArtisanStarting::class);

        $app
            ->shouldReceive('offsetGet')->with('request')->twice()->andReturn($request)
            // ->shouldReceive('offsetGet')->with('events')->twice()->andReturn($events)
            ->shouldReceive('basePath')->andReturn(__DIR__)
            ->shouldReceive('storagePath')->andReturn(__DIR__);

        $request->shouldReceive('ajax')->once()->andReturn(true);

        $output->shouldReceive('writeln')->andReturnUsing(function () use ($exception) {
            throw $exception;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $application = new Application($app, $events, 'testing');
        $application->run($input, $output);
    }
}