<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Budget;
use AppBundle\Entity\User;
use AppBundle\Form\BudgetType;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Rest\Route(requirements={"_format"="json"},)
 * @Rest\RouteResource("Budget", pluralize=false)
 */
class BudgetController extends FOSRestController implements ClassResourceInterface
{


    /**
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTkwNjI5NSwiZXhwIjoxNTA2NDA2Mjk1fQ.iRBoElxVmCj0zlFcwh-cpi-RbeufZm1TnI2HxF0U6V66bgsVudLIvqmMBK2eO8AChdk_IZDVdVRoOgZk-kV3Zah0ClP8rbHvuzk4cwI40et3bbyGNU-IHlmMFpjyLggoq9wDuy3zTwtZuJ8Ux8JEw4CWllv1F4zUe0fja9J9ZgOEGYXVs8b1qF-UzkfU0KUMKiaESEah3n7UR0Gi94B6HQRJh9fWWPYkpr-itV68wZl0z3ZPUoJVYxA9D_vz7SZy-TWieYF1qlc9GrhRS8DFaElz43m_w1DOtcud7RBoVorwh4CYw5VfLp4hraWzCmywsyHipp7a4ErvBOoXFHKfoVrGrvO2O7zVrsCU58AQH7yv1LPv3_UsTLWrdYd7G3AuQrEgtxLX711popz3DeRukdvGCvcbP8v0IzJ-8xs0jVCooyFxtwc_tcav8-liptHJHEdTB23AfHhZ_QTTYuflrpv8wZV5XMO-KzX8zfVmCaN5CXJLU-eXCzMiTMUENZACol4tU6LkQ3gNyxi9oFpalxeamb_nsaWI7EszooFs9cvPNZtXnyGGkMi0bAkdgbxA5EBdrvPnThiU0bMabzVqzKBarKJyLeCURZo9kdIQ2rUVX9qLnEJV28Vx5d1usG9oTyJ1rj_qDdqaoruxSSR17VKBI7blJMlJgbfqy2lOqzs",
     *     in="header",
     *
     * )
     * @SWG\Response (
     *      response=200,
     *      description="Success response - Showing all existing budgets for the user",
     *      @SWG\Schema (
     *          @SWG\Property(
     *               property="id",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="name",
     *               type="string"
     *          ),
     *          @SWG\Property(
     *               property="createdAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="expiredAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="isActive",
     *               type="boolean",
     *               format="boolean"
     *          )
     *      ),
     *      examples={"application/json" : {"id":5, "value": 3500, "createdAt": "2017-09-10T21:06:20.113Z", "expiredAt": "2017-09-29    T21:12:20.113Z","isActive": true}}
     *  ),
     * @SWG\Response(
     *     response=204,
     *     description="Return empty array when no budgets found",
     *  )
     * @SWG\Tag(name="budget")
     *
     *
     *
     */
    public function cgetAction()
    {
        /**
         * @var $budgets Budget[]
         */
        $budgets = $this->getBudgetRepository()->findByUser($this->getUser());


        if (!$budgets) {
                return $this->view($this->get('translator')->trans('budgets.not_found'), Response::HTTP_NO_CONTENT);
        }

        foreach ($budgets as $budget) {
            $budget->setUser(null);
        }

        $user = new User();
        return new View($budgets, 200);


    }

    /**
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNjEwMjIzNywiZXhwIjoxNTA2NjAyMjM3fQ.MkAhvJj4NsABDvlWL559mSU-ocb-QBS5jIjGaTaJC8QuBifwYSZRwjnPdoEuUQRDPzoTMwiSv6EpxHMeYSK55PtC7q5VDbZlK95lxUCtv-_7-kws-hIYKNWdyMAfWAgg2uhvFPLj15H0V22rS6gpCORtVQKrN_KV2v568C6_KhOq0AxBwOV3M5dC-wJPZ0pzFV59iTynP1rrtFlB7jLHHkL42xhb8TTDIsXmd3sg1NX4h3tIqjkvuP8RTkFmVKWnfijDQh5PN8e5myYHOiB9e5QtVhgp7m-f43e0wydxOZhzSwNXwR00OfBDNTHDf3lG69ten4Pq2o06b1WKr75rJ6ajo3jF56CumDVAGcgThSyGuLdK2drnp5EhOiv_CS8KIQGDuccE2fdhxZDI7teyqFcwiQT0ySTXNxnF0ehtOmnykHH-Tugbd-3UvJNuRGVm0lMAM4-9Dscis4ui2R9Y6qdJjcOenArFJn1u-7AAqLF2Dw8ehHWTopgfyizjHNMCrAxuomadDYGL88VxWoVO_tkaraRNPctgCOaYsNprVjiN71tBhxH7B7ayaiMaLQFofFYuh7ZXNVzXxwLDCcwEByZAHlEeFbwZuhPXrc1C5r-jyMR7iWDAgoIx7qvOwHySUcKzQOaX7ehioPj0pdMVoU_3NBMw7JUxAXYg_j8a-LQ",
     *     in="header",
     *)
     *  @SWG\Parameter(
     *     name="Budget Data",
     *     in="body",
     *     description="Creates new budget",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *            type="string",
     *            property="name"
     *          ),
     *          @SWG\Property(
     *            type="integer",
     *            property="value"
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="createdAt",
     *            enum={"year":2016,"month":11,"day":13}
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="expiredAt",
     *            enum={"year":2017,"month":12,"day":5}
     *          ),
     *     ),
     *)
     * @SWG\Response (
     *      response=201,
     *      description="Success response - adds new Budget, and deactivates any previous ones",
     *      @SWG\Schema (
     *          @SWG\Property(
     *               property="value",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="name",
     *               type="string"
     *          ),
     *          @SWG\Property(
     *               property="createdAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="expiredAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *      ),
     *      examples={"application/json" : {"value":500, "name": "Name of the Budget", "expiredAt": {"year": 2017,"month":11,"day":5}, "createdAt": {"year": 2017,"month":12,"day":11}}}
     *  ),
     * @SWG\Response(
     *     response=406,
     *     description="Empty request",
     *  )
     * @SWG\Response(
     *     response=422,
     *     description="In case of incorrect data passed, response with the error message",
     *     examples={"application/json":{"Error": "Incorrect Form-Data: ERROR: ExpiredAt cannot be blank"}}
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Tag(name="budget")
     *
     *
     */
    public function postAction(Request $request)
    {
        /**
         * @var $budget Budget
         */
        $budget = new Budget();

        $postData = $request->request->all();

        if (!$postData) {
            return new View('budget.post.request_empty', 406);
        }


        $form = $this->createForm(BudgetType::class, $budget, [
            'csrf_protection' => false
        ]);
        $form->submit($postData);

        if (!$form->isValid()) {

            return $this->view($this->get('translator')->trans('budget.post.form_error').': ' . $form->getErrors(true), 422);
        }

        /**
         * Sets logged User as related Entity and actives Budget
         */
        $budget->setUser($this->getUser());
        $budget->setIsActive(true);


        /*
         * Making all previous budgets inactive
         */
        $this->deactivatePreviousBudgets();

        $em = $this->getDoctrine()->getManager();
        $em->persist($budget);
        $em->flush();


        return $this->view($this->get('translator')->trans('budget.created_successfully'), Response::HTTP_CREATED);

    }


    /**
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNjEwMjIzNywiZXhwIjoxNTA2NjAyMjM3fQ.MkAhvJj4NsABDvlWL559mSU-ocb-QBS5jIjGaTaJC8QuBifwYSZRwjnPdoEuUQRDPzoTMwiSv6EpxHMeYSK55PtC7q5VDbZlK95lxUCtv-_7-kws-hIYKNWdyMAfWAgg2uhvFPLj15H0V22rS6gpCORtVQKrN_KV2v568C6_KhOq0AxBwOV3M5dC-wJPZ0pzFV59iTynP1rrtFlB7jLHHkL42xhb8TTDIsXmd3sg1NX4h3tIqjkvuP8RTkFmVKWnfijDQh5PN8e5myYHOiB9e5QtVhgp7m-f43e0wydxOZhzSwNXwR00OfBDNTHDf3lG69ten4Pq2o06b1WKr75rJ6ajo3jF56CumDVAGcgThSyGuLdK2drnp5EhOiv_CS8KIQGDuccE2fdhxZDI7teyqFcwiQT0ySTXNxnF0ehtOmnykHH-Tugbd-3UvJNuRGVm0lMAM4-9Dscis4ui2R9Y6qdJjcOenArFJn1u-7AAqLF2Dw8ehHWTopgfyizjHNMCrAxuomadDYGL88VxWoVO_tkaraRNPctgCOaYsNprVjiN71tBhxH7B7ayaiMaLQFofFYuh7ZXNVzXxwLDCcwEByZAHlEeFbwZuhPXrc1C5r-jyMR7iWDAgoIx7qvOwHySUcKzQOaX7ehioPj0pdMVoU_3NBMw7JUxAXYg_j8a-LQ",
     *     in="header",
     *
     *     @SWG\Schema(
     *          @SWG\Property(
     *            type="string",
     *            property="name"
     *          ),
     *          @SWG\Property(
     *            type="integer",
     *            property="value"
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="createdAt",
     *            enum={"year":"number","month":"number","day":"number"}
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="expiredAt",
     *            enum={"year":"number","month":"number","day":"number"}
     *          ),
     *     ),
     *)
     * @SWG\Response (
     *      response=200,
     *      description="Success response - displays budget of requested ID",
     *      @SWG\Schema (
     *          @SWG\Property(
     *               property="id",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="value",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="name",
     *               type="string"
     *          ),
     *          @SWG\Property(
     *               property="createdAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="expiredAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *      ),
     *      examples={"application/json" : {"id":6,"value":500, "name": "Name of the Budget", "expiredAt": {"year": 2017,"month":11,"day":5}, "createdAt": {"year": 2017,"month":12,"day":11}}}
     *  )
     * @SWG\Response(
     *
     *     response=400,
     *     description="In case of nonexistent budget of requested ID, response with and error message",
     *     examples={"application/json":{"Active budget not found"}}
     *  )
     * @SWG\Tag(name="budget"),
     */
    public function getActiveAction()
    {
        $budget = $this->getBudgetRepository()->findByActiveBudgetAndByUser($this->getUser());
        if (!$budget) {
            return $this->view($this->get('translator')->trans('budget.not_found.active'), 400);
        }
        $budget->setUser(null);
        return $this->view($budget, 200);

    }

    /**
     *
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTkwNjI5NSwiZXhwIjoxNTA2NDA2Mjk1fQ.iRBoElxVmCj0zlFcwh-cpi-RbeufZm1TnI2HxF0U6V66bgsVudLIvqmMBK2eO8AChdk_IZDVdVRoOgZk-kV3Zah0ClP8rbHvuzk4cwI40et3bbyGNU-IHlmMFpjyLggoq9wDuy3zTwtZuJ8Ux8JEw4CWllv1F4zUe0fja9J9ZgOEGYXVs8b1qF-UzkfU0KUMKiaESEah3n7UR0Gi94B6HQRJh9fWWPYkpr-itV68wZl0z3ZPUoJVYxA9D_vz7SZy-TWieYF1qlc9GrhRS8DFaElz43m_w1DOtcud7RBoVorwh4CYw5VfLp4hraWzCmywsyHipp7a4ErvBOoXFHKfoVrGrvO2O7zVrsCU58AQH7yv1LPv3_UsTLWrdYd7G3AuQrEgtxLX711popz3DeRukdvGCvcbP8v0IzJ-8xs0jVCooyFxtwc_tcav8-liptHJHEdTB23AfHhZ_QTTYuflrpv8wZV5XMO-KzX8zfVmCaN5CXJLU-eXCzMiTMUENZACol4tU6LkQ3gNyxi9oFpalxeamb_nsaWI7EszooFs9cvPNZtXnyGGkMi0bAkdgbxA5EBdrvPnThiU0bMabzVqzKBarKJyLeCURZo9kdIQ2rUVX9qLnEJV28Vx5d1usG9oTyJ1rj_qDdqaoruxSSR17VKBI7blJMlJgbfqy2lOqzs",
     *     in="header",
     *
     * )
     * @SWG\Response (
     *      response=200,
     *      description="Success response - displays budget of requested ID",
     *      @SWG\Schema (
     *          @SWG\Property(
     *               property="id",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="value",
     *               type="integer"
     *          ),
     *          @SWG\Property(
     *               property="name",
     *               type="string"
     *          ),
     *          @SWG\Property(
     *               property="createdAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="expiredAt",
     *               type="string",
     *               format="date-time"
     *          ),
     *      ),
     *      examples={"application/json" : {"id":6,"value":500, "name": "Name of the Budget", "expiredAt": {"year": 2017,"month":11,"day":5}, "createdAt": {"year": 2017,"month":12,"day":11}}}
     *  )
     * @SWG\Response(
     *
     *     response=400,
     *     description="In case of nonexistent budget of requested ID, response with and error message",
     *     examples={"application/json":{"No budget of ID: 5"}}
     *  )
     * @SWG\Parameter(
     *      name="id",
     *      type="integer",
     *      in="path",
     *      description="Value of requested budget's Id.<br><br> Returns the budget only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Tag(name="budget"),
     *
     * @param  $id int
     * @return View
     */
    public function getAction(int $id)
    {
        /**
         * @var $budget Budget
         */
        $budget = $this->getBudgetRepository()->findByUserAndById($id, $this->getUser());

        if ($budget) {
            $budget->setUser(null);
            return $this->view($budget, 200);
        }
        return $this->view($this->get('translator')->trans('budget.not_found.id').': ' . $id, 400);

    }


    /**
     *
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTkwNjI5NSwiZXhwIjoxNTA2NDA2Mjk1fQ.iRBoElxVmCj0zlFcwh-cpi-RbeufZm1TnI2HxF0U6V66bgsVudLIvqmMBK2eO8AChdk_IZDVdVRoOgZk-kV3Zah0ClP8rbHvuzk4cwI40et3bbyGNU-IHlmMFpjyLggoq9wDuy3zTwtZuJ8Ux8JEw4CWllv1F4zUe0fja9J9ZgOEGYXVs8b1qF-UzkfU0KUMKiaESEah3n7UR0Gi94B6HQRJh9fWWPYkpr-itV68wZl0z3ZPUoJVYxA9D_vz7SZy-TWieYF1qlc9GrhRS8DFaElz43m_w1DOtcud7RBoVorwh4CYw5VfLp4hraWzCmywsyHipp7a4ErvBOoXFHKfoVrGrvO2O7zVrsCU58AQH7yv1LPv3_UsTLWrdYd7G3AuQrEgtxLX711popz3DeRukdvGCvcbP8v0IzJ-8xs0jVCooyFxtwc_tcav8-liptHJHEdTB23AfHhZ_QTTYuflrpv8wZV5XMO-KzX8zfVmCaN5CXJLU-eXCzMiTMUENZACol4tU6LkQ3gNyxi9oFpalxeamb_nsaWI7EszooFs9cvPNZtXnyGGkMi0bAkdgbxA5EBdrvPnThiU0bMabzVqzKBarKJyLeCURZo9kdIQ2rUVX9qLnEJV28Vx5d1usG9oTyJ1rj_qDdqaoruxSSR17VKBI7blJMlJgbfqy2lOqzs",
     *     in="header",
     * )
     *
     *  @SWG\Parameter(
     *     name="Budget Data",
     *     in="body",
     *     description="Creates new budget",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *            type="string",
     *            property="name"
     *          ),
     *          @SWG\Property(
     *            type="integer",
     *            property="value"
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="createdAt",
     *            enum={"year":2016,"month":11,"day":13}
     *          ),
     *          @SWG\Property(
     *            type="string",
     *            property="expiredAt",
     *            enum={"year":2017,"month":12,"day":5}
     *          ),
     *     ),
     *)
     * @SWG\Response(
     *     response=422,
     *     description="In case of incorrect data passed, response with the error message",
     *     examples={"application/json":{"Error": "Incorrect Form-Data: ERROR: ExpiredAt cannot be blank"}}
     *  )
     * @SWG\Response (
     *      response=200,
     *      description="Success response - edited budget of requested ID",
     *      examples={"application/json":{"Bug"}}
     *     )
     * @SWG\Response(
     *     response=400,
     *     description="In case of nonexistent budget of requested ID, response with and error message",
     *     examples={"application/json":{"No budget of ID: 5"}}
     *  )
     * @SWG\Parameter(
     *      name="id",
     *      type="integer",
     *      in="path",
     *      description="Value of requested budget's Id.<br><br> Returns the budget only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Tag(name="budget"),
     *
     * @param  $id int
     * @param $request Request
     * @return View
     */
    public function putAction($id, Request $request)
    {

        $budget = $this->getBudgetRepository()->find($id);
        if(!$budget) {
            return $this->view($this->get('translator')->trans('budget.not_found.id').': ' . $id, 400);
        }


        $form = $this->createForm(BudgetType::class, $budget, ['csrf_protection' => false]);
        $form->submit($request->request->all());

        if(!$form->isValid()){
            return $this->view($form->getErrors(true),422);
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $budget->setUser(null);

        return $this->view($budget, 200);

    }
    /**
     *
     * @SWG\Parameter(
     *     name="Authorization",
     *     required=true,
     *     type="string",
     *     description="Authorization header needs to have a value of a string: <strong>Bearer Token</strong> - Try It Out to see an example",
     *     default="Bearer eyJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkB3cC5wbCIsImlhdCI6MTUwNTkwNjI5NSwiZXhwIjoxNTA2NDA2Mjk1fQ.iRBoElxVmCj0zlFcwh-cpi-RbeufZm1TnI2HxF0U6V66bgsVudLIvqmMBK2eO8AChdk_IZDVdVRoOgZk-kV3Zah0ClP8rbHvuzk4cwI40et3bbyGNU-IHlmMFpjyLggoq9wDuy3zTwtZuJ8Ux8JEw4CWllv1F4zUe0fja9J9ZgOEGYXVs8b1qF-UzkfU0KUMKiaESEah3n7UR0Gi94B6HQRJh9fWWPYkpr-itV68wZl0z3ZPUoJVYxA9D_vz7SZy-TWieYF1qlc9GrhRS8DFaElz43m_w1DOtcud7RBoVorwh4CYw5VfLp4hraWzCmywsyHipp7a4ErvBOoXFHKfoVrGrvO2O7zVrsCU58AQH7yv1LPv3_UsTLWrdYd7G3AuQrEgtxLX711popz3DeRukdvGCvcbP8v0IzJ-8xs0jVCooyFxtwc_tcav8-liptHJHEdTB23AfHhZ_QTTYuflrpv8wZV5XMO-KzX8zfVmCaN5CXJLU-eXCzMiTMUENZACol4tU6LkQ3gNyxi9oFpalxeamb_nsaWI7EszooFs9cvPNZtXnyGGkMi0bAkdgbxA5EBdrvPnThiU0bMabzVqzKBarKJyLeCURZo9kdIQ2rUVX9qLnEJV28Vx5d1usG9oTyJ1rj_qDdqaoruxSSR17VKBI7blJMlJgbfqy2lOqzs",
     *     in="header",
     * )
     * @SWG\Response (
     *      response=200,
     *      description="Success response - deletes budget of requested ID",
     *      examples={"application/json":{"Bug"}}
     *     )
     * @SWG\Response(
     *     response=400,
     *     description="In case of nonexistent budget of requested ID, response with and error message",
     *     examples={"application/json":{"No budget of ID: 5"}}
     *  )
     * @SWG\Parameter(
     *      name="id",
     *      type="integer",
     *      in="path",
     *      description="Value of requested budget's Id.<br><br> Deletes the budget only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Tag(name="budget"),
     *
     */
    public function deleteAction($id)
    {
            $budget = $this->getBudgetRepository()->findByUserAndById($id, $this->getUser());
            if(!$budget) {
                return $this->view($this->get('translator')->trans('budget.not_found.id').': ' . $id, 400);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($budget);
            $em->flush();

            return $this->view($this->get('translator')->trans('budget.removed_successfully').': '.$id);
    }


    private function getBudgetRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Budget');
    }

    private function deactivatePreviousBudgets()
    {
        /**
         * @var $budgets Budget[]
         */
        $budgets = $this->getBudgetRepository()->findByActiveBudgetAndByUser($this->getUser());

        if ($budgets) {

            /**
             * @var $em EntityManager
             */
            $em = $this->getDoctrine()->getManager();

            foreach ($budgets as $budget) {
                $budget->setIsActive(false);
                $em->persist($budget);
            }

            $em->flush();
        }
    }
}
