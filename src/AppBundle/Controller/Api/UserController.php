<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Rest\Route(requirements={"_format"="json|xml"})
 * @Rest\RouteResource("User", pluralize=false)
 */
class UserController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @Annotations\Post("/register")
     * @SWG\Parameter(
     *     name="Register Credentials",
     *     in="body",
     *     description="Registers new user when correct username and password is passed",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *            type="string",
     *            property="email"
     *          ),
     *          @SWG\Property(
     *          property="plainPassword",
     *          type="string",
     *          enum={"first":"string","second" : "string"}
     *          )
     *     ),
     *  )
     * @SWG\Response (
     *      response=201,
     *      description="Success response",
     *      examples={"application/json" : { "msg": "Your account has been created successfully."}},
     *  ),
     * @SWG\Response(
     *     response=400,
     *     description="Error message regarding incorrect login data ",
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="integer",
     *          property="code"
     *          ),
     *      @SWG\Property(
     *          type="string",
     *          property="message"
     *          ),
     *      ),
     *     examples={"application/json" : {"code":400,"message":"Validation Failed","errors":{"errors":{"Password cannot be the same as email","Please enter a username."},"children":{"email":{"errors": {"Please enter an email."}},"plainPassword":{"children":{"first":{"errors":{"This value should not be blank.","Please enter a password."}},"second":{}}}}}} }
     *  )
     * @SWG\Tag(name="user")
     *
     *
     * @param Request $request
     * @return null|\Symfony\Component\Form\FormInterface|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm([
            'csrf_protection' => false
        ]);

        $form->setData($user);
        $form->submit($request->request->all());

        if (!$form->isValid()) {

            $event = new FormEvent($form, $request);

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $form;
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }

        $userManager->updateUser($user);

        $response = new JsonResponse(
            [
                'msg' => $this->get('translator')->trans('registration.flash.user_created', [], 'FOSUserBundle'),
//                'token' => 'abc-123' // some way of creating the token
            ],
            JsonResponse::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'get_user_profile',
                    ['id' => $user->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );

        $dispatcher->dispatch(
            FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, $response)
        );

        return $response;
    }

    /**
     * @Annotations\Get("/profile")
     * @SWG\Response (
     *      response=200,
     *      description="Success response",
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="integer",
     *          property="id"
     *          ),
     *      @SWG\Property(
     *          type="string",
     *          property="email"
     *          ),
     *       ),
     *   examples={"application/json" : { "id": 5,"email":"username@email.com"}}
     *     )
     * @SWG\Tag(name="user")
     *
     *
     *
     */
    public function getProfileAction()
    {

        /**
         * @var $user User
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->view($this->get('translator')->trans('user.not.exists'), 400);
        }
        return $this->view([
            "id" => $user->getId(),
            "email" => $user->getUsername()
        ], 200);
    }

    /**
     * @Annotations\Put("/profile/edit")
     * @SWG\Response (
     *      response=200,
     *      description="Success response",
     *      @SWG\Schema (
     *          @SWG\Property(
     *            type="current_password",
     *            property="email"
     *          ),
     *          @SWG\Property(
     *          property="plainPassword",
     *          type="string",
     *          enum={"first":"string","second" : "string"}
     *          )
     *       ),
     *   examples={"application/json" : { "id": 5,"email":"username@email.com"}}
     *     )
     * @SWG\Tag(name="user")
     *
     *
     *
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(ChangePasswordFormType::class, $user, ['csrf_protection' => false]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), 400);
        }
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_profile_show');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $this->view('password.changed.successfully', 200);


    }


}
