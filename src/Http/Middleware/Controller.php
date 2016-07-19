<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class Controller
 *
 * Run proper controller based on request.
 *
 * @package TypeRocket\Http\Middleware
 */
class Controller extends Middleware
{

    public function handle()
    {
        $request = $this->request;
        $response = $this->response;

        $method = $request->getMethod();
        $action = null;
        if( $request->getAction() == 'auto' ) {
            switch ($method) {
                case 'PUT' :
                    $action = 'update';
                    break;
                case 'GET' :
                    $action = 'read';
                    break;
                case 'DELETE' :
                    $action = 'delete';
                    break;
                case 'POST' :
                    $action = 'create';
                    break;
            }
        } else {
            switch ( $request->getAction() ) {
                case 'create' :
                    if( $method == 'POST' ) {
                        $action = 'create';
                    } else {
                        $action = 'add';
                    }
                    break;
                case 'update' :
                    if( $method == 'PUT' ) {
                        $action = 'update';
                    } else {
                        $action = 'edit';
                    }
                    break;
                case 'delete' :
                    if( $method == 'DELETE' ) {
                        $action = 'update';
                    }
                    break;
                case 'index' :
                    if( $method == 'GET' ) {
                        $action = 'index';
                    }
                    break;
                case 'read' :
                    if( $method == 'GET' ) {
                        $action = 'read';
                    }
                    break;
            }
        }


        $resource = ucfirst( $request->getResource() );
        $controller  = "\\TypeRocket\\Controllers\\{$resource}Controller";

        if( ! class_exists( $controller ) ) {
            $controller  = "\\" . TR_APP_NAMESPACE . "\\Controllers\\{$resource}Controller";
        }

        if ($response->getValid() && class_exists( $controller ) ) {
            $controller = new $controller( $request, $response);
            $id         = $request->getResourceId();

            if ($controller instanceof \TypeRocket\Controllers\Controller && $response->getValid()) {
                if (method_exists( $controller, $action )) {
                    $controller->$action( $id );
                } else {
                    $response->setError( 'controller', 'The method specified is not allowed for the resource.');
                    $this->response->setStatus(405);
                    $this->response->setInvalid();
                }
            }

        }

    }

}