<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{

    public function __construct($budgets)
    {
        $this->budgets = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    protected $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Budget", mappedBy="user")
     */
    protected $budgets;

    /**
     * @return ArrayCollection
     */
    public function getBudgets(){
        return $this->budgets;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }
    /**
     * @Assert\IsTrue(message = "fos.user.email.matches_password")
     */
    public function isPasswordLegal()
    {
        return $this->plainPassword !== $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
        $this->username = $email;
    }

    public function setEmailCanonical($emailCanonical){
        $this->emailCanonical = $emailCanonical;
        $this->usernameCanonical = $emailCanonical;
    }



}