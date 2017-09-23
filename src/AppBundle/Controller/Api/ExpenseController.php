<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Budget;
use AppBundle\Entity\Expense;
use AppBundle\Form\ExpenseType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route(requirements={"_format"="json"},)
 * @Rest\RouteResource("Expense", pluralize=false)
 */
class ExpenseController extends FOSRestController implements ClassResourceInterface

{
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
                return $this->view(['error' => $this->get('translator')->trans('expense.not_found')],204);
            }
            foreach ($expenses as $expense) {
                $expense->setBudget(null);
                $budgetsExpenses[] = $expense;
            }
        }
        return $this->view($budgetsExpenses, 200);
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function getAction($id)
    {
        $expense = $this->getExpenseRepository()->find($id);

        $user = $this->getUser();

        if (!$expense || !$expense->getBudget()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }

        if($expense->getBudget()->getUser()->getId() !== $this->getUser()->getId()){
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }
        $expense->setBudget(null);
        return $this->view( $expense, 200);
    }

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

    public function putAction($id, Request $request)
    {
        $expense = $this->getExpenseRepository()->find($id);
        if (!$expense || !$expense->getBudget()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }

        if ($expense->getBudget()->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->view(['error' => $this->get('translator')->trans('expense.not_found') . ': id'], 400);
        }


        $form = $this->createForm(ExpenseType::class, $expense, ['csrf_protection' => false]);
        $form->submit($request->request->all());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->view($this->get('translator')->trans('expense.updated'), 200);
    }

    public function deleteAction($id)
    {
        $expense = $this->getExpenseRepository()->find($id);
        if (!$expense || !$expense->getBudget()) {
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
