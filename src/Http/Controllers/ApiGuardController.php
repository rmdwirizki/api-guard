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
    protected $template;

    public function __construct(Request $request)
    {
        $serializedApiMethods = serialize($this->apiMethods);

        // Launch middleware
        $this->middleware('apiguard:' . $serializedApiMethods);

        // Attempt to get an authenticated user.
        $this->user = ApiGuardAuth::getUser();

        $this->response = ApiResponseBuilder::build();
        
        $this->template = $request->get('template');    
    }

    public function json($data, $transformer, $isItem=false)
    {
        if($this->template !== NULL) {
            $template = ['template' => $this->template];
        }
        else {
            $template = [];
        }
        
        if (!$isItem){
            return $this->response->withCollection(
                $data, 
                $transformer,
                null,
                null,
                $template
            );
        }else{
            return $this->response->withItem(
                $data, 
                $transformer,
                null,
                $template
            );
        }
    }
}