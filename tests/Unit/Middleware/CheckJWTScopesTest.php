<?php

namespace Tests\Unit\Middleware;

use App\Entities\Auth\JWT\Scope;
use App\Entities\Auth\JWT\Token;
use App\Http\Middleware\CheckJWTScopes;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckJWTScopesTest extends TestCase
{
    /** @test */
    public function itAllowsValidJWTsInAuthorizationHeader()
    {
        $token = new Token('testing', Carbon::now()->addMinute()->isoFormat('X'), 'test',
            collect([new Scope('tester-scope')]));

        $middleware = new CheckJWTScopes();

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', "Bearer {$token->encode()}");

        $response = $middleware->handle($request, function () {
            return response([], 200);
        }, 'tester-scope');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @test */
    public function itAllowsValidJWTsInTokenQueryParam()
    {
        $token = new Token('testing', Carbon::now()->addMinute()->isoFormat('X'), 'test',
            collect([new Scope('tester-scope')]));

        $middleware = new CheckJWTScopes();

        $request = Request::create("/", 'GET', ['token' => $token->encodeUrlSafe()]);

        $response = $middleware->handle($request, function () {
            return response([], 200);
        }, 'tester-scope');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @test */
    public function itRejectsInvalidJWTsInAuthorizationHeader()
    {
        try {
            $token = new Token(
                'testing',
                Carbon::now()->subMinute()->toIsoString('X'),
                'test',
                collect([new Scope('tester-scope')]),
                Carbon::now()->subMinutes(2)->toIsoString('X')
            );
        } catch (Exception $e) {
            $this->fail('Failed to create token');
        }

        $middleware = new CheckJWTScopes();

        $request = Request::create(
            '/',
            'GET',
            ['headers' => ['Authorization' => "Bearer {$token->encode()}"]]
        );

        /** @var Response $response */
        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function itAllowsSingleScopes()
    {
        $token = new Token(
            'testing',
            Carbon::now()->addMinute()->isoFormat('X'),
            'test',
            collect([new Scope('tester-scope')])
        );

        $middleware = new CheckJWTScopes();

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', "Bearer {$token->encode()}");

        $response = $middleware
            ->handle(
                $request,
                function () {
                    return response([], 200);
                },
                'tester-scope'
            );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @test */
    public function itAllowsMultipleScopes()
    {
        $token = new Token(
            'testing',
            Carbon::now()->addMinute()->isoFormat('X'),
            'test',
            collect([new Scope('tester-scope'), new Scope('test2-scope')])
        );

        $middleware = new CheckJWTScopes();

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', "Bearer {$token->encode()}");

        $response = $middleware
            ->handle(
                $request,
                function () {
                    return response([], 200);
                },
                'tester-scope',
                'test2-scope'
            );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /** @test */
    public function itDisallowsWithOnlyOneValidScope()
    {
        $token = new Token(
            'testing',
            Carbon::now()->addMinute()->isoFormat('X'),
            'test',
            collect([new Scope('tester-scope')])
        );

        $middleware = new CheckJWTScopes();

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', "Bearer {$token->encode()}");

        $response = $middleware
            ->handle(
                $request,
                function () {
                    return response([], 200);
                },
                'tester-scope',
                'missing-scope'
            );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
