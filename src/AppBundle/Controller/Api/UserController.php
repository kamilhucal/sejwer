<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Swagger\Annotations as SWG;


/**
 * @Rest\Route(requirements={"_format"="json|xml"})
 */
class UserController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @Annotations\Post("/register")
     *  @SWG\Parameter(
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
     *  @SWG\Response (
     *      response=201,
     *      description="Success response - Receiving JWT Token, for future validation",
     *      examples={"application/json" : {"token": "eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTg5ODI4OCwiZXhwIjoxNTA2Mzk4Mjg4fQ.IJlxMu-dDXEy9wW_JDEWzgFTDfpFd3Oe2IUl-jXXT_vDiB6MQx4d0mOVbC3K16_nWyVc3qjtLoWLn48QHdHiedIvwf_MY_W3zFs3JQXJ5aJdcAGDu1Zks8n3D6x22M1kxwJ0erXlS8dRUwQmeeYElhMJrVymyKUsMA1yxDFOrC7f-8wCvIDVgPNckkji6DiC3i5eVcK7GQ1PYxgx-be2TU7KrPaMqbI1DOeBaxUOAx5xRbslQPMTDuerrrTjFh9fHmUzwYw_wNDBYeb3ZxwBwov4fG00frhb1axUJvZY28fpvX5Z1dd26z_0ScxTKP4NbObaZ-eHvRBzPAb4xiE-fVXXO-V4iLqfnWPXQjB7tXTK_mVp40OjQdzWW1Oj0hZbfyBvn7duP3pn-jBy9hISxd_jAjkWXASZL9NuN1PxvQtvW92vTmcFBEIBTvIl6zIIqb1XBY3_qoAa0XCr6IhOf1KHqFmLLsRBhqulYfY5O8-8lrIsafMfECQ5hmD0ec3U6SLQadPeH4OCAboFPSqKWPzN4kz7ii-9KPrxQW8kwCH-8yxdb8pxmMmmKaUXZiJQfB1edlbWflj7ZzoObARq9Yeubk0b6Qm7fsaTLIzBId4RQdjdXuscG_FbzkkZWAa54YwROcKQGAcZB8quNJK-wWvEeLoUup0SQ6BgrU57fdo"}},
     *      @SWG\Schema (
     *          @SWG\Property(
     *          type="string",
     *          property="token"
     *          ),
     *      ),
     *  ),
     *  @SWG\Response(
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
     *  @SWG\Tag(name="user")
     *
     *
     *
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
            'csrf_protection'    => false
        ]);

        $form->setData($user);
        $form->submit($request->request->all());

        if ( ! $form->isValid()) {

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
                    [ 'id' => $user->getId() ],
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
     * @Annotations\Get("/profile/{id}")
     * @param $id integer
     *
     */
    public function getProfileAction($id){

        //TODO: ANNOTATIONS + IF IS WORTH IT TO HAVE IT LIKE THAT
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if(!$user)
        {

        }
    }




}
