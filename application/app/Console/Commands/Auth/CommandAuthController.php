<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 02/08/18
	 * Time: 14.48
	 */

	namespace App\Http\Controller\RESTful\v1;

	use App\Http\Controller\RESTful\RestController;
	use App\Repositories\UserRepository;
	use ACLService;
	use Illuminate\Http\Request;

	class AuthController extends RestController
	{
		/** User Repository
		 *
		 * @var User
		 */
		private $model;


		/**
		 * @Var auth instance of Auth service
		 */
		public function __construct()
		{
			parent::__construct();
			$this->model = app(UserRepository::class);
		}

		/**
		 * User authentication with JWT.
		 * Returns the token or an error response
		 *
		 * @param Request $request
		 *
		 * @return mixed (token) or (errors)
		 */
		public function authenticate(Request $request)
		{
			$credentials = $request->only('email', 'password');
			try {
				$token = $this->auth->jwt->attempt($credentials);
				if (!$token)
					return $this->response()->errorNotFound();
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				return $this->response()->errorInternal('Could not create token.');
			}
			return $this->response()->successData(compact('token'));
		}


		/**
		 * Register method.
		 * The request is validated by the user repository method, then it is created and then authenticated with JWT
		 * which creates the token. If the token is successfully created, the roles and permssi are assigned to the
		 * user. Return a reply with the user and the token
		 *
		 * @param Request $request
		 *
		 * @return mixed (user + token) or (errors)
		 */
		public function register(Request $request)
		{

			$validator = $this->model->validateRequest($request->all(), "create");

			if ($validator->isSuccess()) {
				$user = $this->model->create($request->all());

				$token = $this->auth->jwt->fromUser($user);
				if (!$token)
					return $this->response()->errorInternal();

				return $this->response()->successData(compact('user', 'token'));
			}
			return $this->response()->withValidation($validator->data(), true)->errorBadRequest();
		}

		/**
		 * Method for check user authenticated.
		 * Return user playload or Exception
		 *
		 * @return \Illuminate\Http\JsonResponse
		 */
		public function getAuthenticatedUser()
		{
			return $this->auth->getUser();
		}

		/**
		 * @return mixed
		 */
		public function invalidate()
		{
			$status = $this->auth->invalidate(true);

			if ($status->isSuccess())
				return $this->response()->success()->withMessage($status->message());
			else
				return $this->response()->errorException("Error not invalidate");
		}

		/**
		 * @return mixed
		 */
		public function refresh()
		{
			$status = $this->auth->refresh(true);

			if ($status->isSuccess())
				return $this->response($status->data('token'))
					->withMessage($status->message())
					->success();
			else
				return $this->response()->errorException("Error not refreshed");
		}
	}