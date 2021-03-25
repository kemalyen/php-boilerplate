<?php
declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="roles",uniqueConstraints={@ORM\UniqueConstraint(name="unique_name", columns={"name"}, options={"where": "(((id IS NOT NULL) AND (name IS NULL))"})})
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $display_name;

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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @param mixed $display_name
     */
    public function setDisplayName($display_name): void
    {
        $this->display_name = $display_name;
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
    public function setUpdateddAt($date)
    {
        $this->updated_at = $date;

        return $this;
    }

}