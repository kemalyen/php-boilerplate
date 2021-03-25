<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="users",uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email"}, options={"where": "(((id IS NOT NULL) AND (email IS NULL))"})})
 * @ORM\HasLifecycleCallbacks()
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="App\Entities\Role")
     * @ORM\JoinTable(name="users_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated_at;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid): void
    {
        $this->uid = $uid;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles->add($role);
        return $this;
    }

    public  function addRoles($roles){
        $this->roles = $roles;
    }

    public function removeRoles()
    {
     foreach ($this->roles as $role){
         $this->removeRole($role);
     }
    }

    public function removeRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            return;
        }
        $this->roles->removeElement($role);
        //$role->removeUser($this);
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Get $created
     *
     * @return  datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set $created
     *
     * @param  datetime  $created_at  $created
     *
     * @return  self
     */
    public function setCreatedAt($date)
    {
        $this->created_at = $date;

        return $this;
    }

    /**
     * Set $date
     *
     * @param  datetime  $date_at
     *
     * @return  self
     */
    public function setUpdatedAt($date)
    {
        $this->updated_at = $date;

        return $this;
    }
}
