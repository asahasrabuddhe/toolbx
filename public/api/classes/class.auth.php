<?php

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Http\Stream;

class Auth implements ServiceProviderInterface
{
	public function __construct()
	{

	}

	public function register( Container $container )
	{
		$container['basic_auth'] = $this;
	}

	public function __invoke( $request, $response, $next )
	{
		$uri = $request->getUri();

		if( 1 )
		{
			$headers = $request->getHeaders();

			if( isset( $headers['PHP_AUTH_USER'] ) && !empty( $headers['PHP_AUTH_USER'][0] ) && isset( $headers['PHP_AUTH_PW'] ) && !empty( $headers['PHP_AUTH_PW'][0] ) )
			{
				$user_id = $headers['PHP_AUTH_USER'][0];
				$token = $headers['PHP_AUTH_PW'][0];

				$data = verify_token( $user_id, $token );

				if( ! $data )
				{
					$body = $response->getBody();
					$body->write( json_encode( array ( 'message_code' => 107 ) ) );
					return $response->withBody( $body )->withStatus( 400 );
				}
			}
			else
			{
				return $response->withStatus( 401 )->withHeader( 'WWW-Authenticate', 'Basic realm="ToolBx"' );
			}
		}

		return $next( $request, $response );
	}
}