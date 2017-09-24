<?php

namespace AppBundle\EventListener;


use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;


/**
 * Checks if the logged in user's budgets is still active due to expiry date
 */
class ExpiryDateListener implements EventSubscriberInterface
{


    protected $entityManager;
    protected $userManager;

    private $tokenStorage;

    public function __construct(UserManagerInterface $userManager, EntityManager $entityManager, SecurityContextInterface $securityContext = null, TokenStorageInterface $tokenStorage = null)
    {

        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage ?: $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'expiration',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            KernelEvents::CONTROLLER => 'onRequest',
        );
    }

    /**
     * @param UserEvent $event
     */
    public function expiration(UserEvent $event)
    {
        $user = $event->getUser();
        if (!$user) {
            return;
        }
        $this->expiryBudgetDateResolver($user);
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user) {
            return;
        }
        $this->expiryBudgetDateResolver($user);

    }

    /**
     *
     */
    public function onRequest()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }
        $user = $token->getUser();

        if ($user === "anon.") {
            return;
        }
        $this->expiryBudgetDateResolver($user);
    }


    /**
     * @param $user
     * Finding if the active budgets is past the expiry date, and deactivates it.
     */
    private function expiryBudgetDateResolver($user)
    {

        $activeBudgets = $this->entityManager->getRepository('AppBundle:Budget')->findByActiveBudgetsAndByUser($user);
        if ($activeBudgets) {
            foreach($activeBudgets as $activeBudget){
                if ($this->isExpired($activeBudget->getExpiredAt())) {
                    $activeBudget->setIsActive(false);
                };
                $this->entityManager->persist($activeBudget);

            }
            $this->entityManager->flush();
        }
    }

    /**
     * @param $date
     * @return bool True when Date is past the expiry date
     */
    private function isExpired($date)
    {
        /**
         * @var boolean
         */
        return ($date < new \DateTime());
    }


}



