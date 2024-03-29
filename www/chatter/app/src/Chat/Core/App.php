<?php

namespace Chat\Core;


use Chat\Common\Enums\Reference;
use Chat\Common\Helpers\Url;
use Chat\Front\Http\Request;
use Chat\Front\Http\Response;

class App
{
	/** @var Request */
	private $request;
	/** @var Response */
	private $response;

	public function __construct()
	{
		$this->request = ServiceBinder::bind(Request::class);
		$this->response = ServiceBinder::bind(Response::class);
	}

    /**
     * @return mixed
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
	public function run()
	{
		$route= ucfirst($this->request->get(Reference::CONTROLLER_QUERY_PARAM));
		$action = str_replace('_', '', ucwords($this->request->get(Reference::ACTION_QUERY_PARAM)));
		if (!class_exists("Chat\\Front\\Controllers\\{$route}Controller")) {
			$this->response->redirect(Url::createLinkToAction('user','all'));
		}
		$controller = ServiceBinder::bind("Chat\\Front\\Controllers\\{$route}Controller");
		if (!method_exists($controller, 'get'.$action)) {
			$this->response->notFound();
		}
		$action = 'get'.$action;

		return $controller->$action();
	}
}