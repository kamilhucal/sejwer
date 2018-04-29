<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Budget;
use AppBundle\Entity\Expense;
use AppBundle\Form\ExpenseType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

/**
 * @Rest\Route(requirements={"_format"="json"},)
 * @Rest\RouteResource("Expense", pluralize=false)
 */
class ExpenseController extends FOSRestController implements ClassResourceInterface
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
     *      description="Success response - Showing all existing user's expenses",
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
     *     description="Return empty array when no expenses found",
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Tag(name="expense")
     *
     *
     *
     */
    public function cgetAction()
    {

        /**
         * @var $budgets Budget[]
         */
        $budgets = $this->getUser()->getBudgets();


        if (!$budgets) {
            return $this->view(['error' => $this->get('translator')->trans('budgets.not_found')], 400);
        }

        /**
         * @var $budgetsExpenses Expense[]
         */
        $budgetsExpenses = [];

        foreach ($budgets as $budget) {
            /**
             * @var $expenses Expense[]
             */
            $expenses = $budget->getExpenses();
            if (!$expenses) {
                return $this->view(array('error' => $this->get('translator')->trans('expense.not_found')),204);
            }
            foreach ($expenses as $expense) {
                $expense->setBudget(null);
                $budgetsExpenses[] = $expense;
            }

        }
        return $this->view($budgetsExpenses, 200);
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
     *      description="Success response - displays expense of requested ID",
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
     *      ),
     *      examples={"application/json" : {"id":6,"value":500, "name": "Name of the Expense", "created_at": {"year": 2017,"month":12,"day":11}}}
     *  )
     * @SWG\Response(
     *
     *     response=400,
     *     description="In case of nonexistent expense of requested ID, response with and error message",
     *     examples={"application/json":{"No expense of ID: 5"}}
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Parameter(
     *      name="id",
     *      type="integer",
     *      in="path",
     *      description="Value of requested budget's Id.<br><br> Returns the expense only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Tag(name="expense"),
     *
     * @param  $id int
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction($id)
    {
        $expense = $this->getExpenseRepository()->findOneById($id);


        if (!$expense || !$expense->getBudget() || !$expense->getBudget()->getUser()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }

        if(($expense->getBudget())->getUser()->getId() !== $this->getUser()->getId()){
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }
        $expense->setBudget(null);
        return $this->view( $expense, 200);
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
     * @SWG\Parameter(
     *     name="Budget Data",
     *     in="body",
     *     description="Creates new expense",
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
     *     ),
     *)
     * @SWG\Response (
     *      response=201,
     *      description="Success response - adds new Expense",
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
     *               property="created_at",
     *               type="string",
     *               format="date-time"
     *          ),
     *          @SWG\Property(
     *               property="expired_at",
     *               type="string",
     *               format="date-time"
     *          ),
     *      ),
     *      examples={"application/json" : {"value":500, "name": "Name of the Expense", "created_at": {"year": 2017,"month":12,"day":11}}}
     *  ),
     * @SWG\Response(
     *     response=406,
     *     description="Empty request",
     *  )
     * @SWG\Response(
     *     response=422,
     *     description="In case of incorrect data passed, response with the error message",
     *     examples={"application/json":{"Error": "Incorrect Form-Data: ERROR: CreatedAt cannot be blank"}}
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Tag(name="expense")
     *
     *
     */
    public function postAction(Request $request)
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense, ['csrf_protection' => false]);

        $form->submit($request->request->all());
        if(!$form->isValid()){
            return $this->view($this->get('translator')->trans('expense.post.unsuccessful'). ': '. $form->getErrors(true), 400);
        }

        $activeBudget = $this->getBudgetRepository()->findByActiveBudgetAndByUser($this->getUser());

        if(!$activeBudget){
            return $this->view($this->get('translator')->trans('budget.not_found.active'), 400);
        }
        $expense->setBudget($activeBudget);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($expense);
        $entityManager->flush();

        return $this->view($this->get('translator')->trans('expense.created'),200);
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
     * @SWG\Parameter(
     *     name="Budget Data",
     *     in="body",
     *     description="Edits existing Expense",
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
     *     ),
     *)
     * @SWG\Response(
     *     response=422,
     *     description="In case of incorrect data passed, response with the error message",
     *     examples={"application/json":{"Error": "Incorrect Form-Data: ERROR: CreatedAt cannot be blank"}}
     *  )
     * @SWG\Response (
     *      response=200,
     *      description="Success response - edited budget of requested ID",
     *      examples={"application/json":{"Expense"}}
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
     *      description="Value of requested budget's Id.<br><br> Returns the expense only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Tag(name="expense"),
     *
     * @param  $id int
     * @param $request Request
     * @return View
     */
    public function putAction($id, Request $request)
    {
        $expense = $this->getExpenseRepository()->find($id);
        if (!$expense || !$expense->getBudget() || !$expense->getBudget()->getUser()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }

        if ($expense->getBudget()->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }
        $form = $this->createForm(ExpenseType::class, $expense, ['csrf_protection' => false]);
        $form->submit($request->request->all());
        if(!$form->isValid()){
            return $this->view($this->get('translator')->trans('expense.post.unsuccessful'). ': '. $form->getErrors(true), 400);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->view($this->get('translator')->trans('expense.updated'), 200);
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
     *      description="Success response - deletes expense of requested ID",
     *      examples={"application/json":{"Bug"}}
     *     )
     * @SWG\Response(
     *     response=400,
     *     description="In case of nonexistent expense of requested ID, response with and error message",
     *     examples={"application/json":{"No expense of ID: 5"}}
     *  )
     * @SWG\Parameter(
     *      name="id",
     *      type="integer",
     *      in="path",
     *      description="Value of requested expense's Id.<br><br> Deletes the budget only if <strong>logged user is its owner</strong>"
     *
     *  )
     * @SWG\Response(
     *     response=401,
     *      @SWG\Header(header="WWW-Authenticate", type="string", description="first",default="Bearer"),
     *     description="Invalid Token / Not Token",
     *     examples={"application/json":{"code":401,"message":"Bad credentials"}}
     *  )
     * @SWG\Tag(name="expense"),
     *
     */
    public function deleteAction($id)
    {
        $expense = $this->getExpenseRepository()->find($id);
        if (!$expense || !$expense->getBudget() || !$expense->getBudget()->getUser()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }

        if ($expense->getBudget()->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($expense);
        $entityManager->flush();

        return $this->view($this->get('translator')->trans('expense.deleted'), 200);

    }

    private function getExpenseRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Expense');
    }

    private function getBudgetRepository()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Budget');
    }
}
