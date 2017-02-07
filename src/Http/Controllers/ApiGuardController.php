<?php

namespace Chrisbjr\ApiGuard\Http\Controllers;

use ApiGuardAuth;
use Chrisbjr\ApiGuard\Builders\ApiResponseBuilder;
use Illuminate\Routing\Controller;
use EllipseSynergie\ApiResponse\Laravel\Response;

use Illuminate\Http\Request;

class ApiGuardController extends Controller
{

    /**
     * @var Response
     */
    public $response;

    /**
     * The authenticated user
     *
     * @var
     */
    public $user;

    /**
     * @var array
     */
    protected $apiMethods;
    protected $meta;

    public function __construct(Request $request)
    {
        $serializedApiMethods = serialize($this->apiMethods);

        // Launch middleware
        $this->middleware('apiguard:' . $serializedApiMethods);

        // Attempt to get an authenticated user.
        $this->user = ApiGuardAuth::getUser();

        $this->response = ApiResponseBuilder::build();
        
        $this->meta = ['template' => $request->get('template')];    
    }

    public function json($data, $transformer, $isItem=false, $withMeta=true)
    {
        if (!$withMeta) {
            $this->meta = [];
        }
        
        if (!$isItem){
            return $this->response->withCollection(
                $data, 
                $transformer,
                null,
                null,
                $this->meta
            );
        }else{
            return $this->response->withItem(
                $data, 
                $transformer,
                null,
                $this->meta
            );
        }
    }
}