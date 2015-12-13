<?php

namespace Auth\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Rbac\Role\HierarchicalRoleInterface;
use ZfcRbac\Permission\PermissionInterface;

/**
 * @ORM\Table(name="auth_role")
 * @ORM\Entity
 */
class Role implements HierarchicalRoleInterface
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=48, unique=true)
     */
    protected $name;
    /**
     * @var HierarchicalRoleInterface[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Auth\Entity\Role")
     * @ORM\JoinTable(name="auth_role_roles")
     */
    protected $children = [];
    /**
     * @var PermissionInterface[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Auth\Entity\Permission", indexBy="name", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinTable(name="auth_role_permission",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $permissions;
    /**
     * Init the Doctrine collection
     */
    public function __construct()
    {
        $this->children    = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }
    /**
     * Get the role identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set the role name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }
    /**
     * Get the role name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * {@inheritDoc}
     */
    public function addChild(HierarchicalRoleInterface $child)
    {
        $this->children[] = $child;
    }
    /**
     * {@inheritDoc}
     */
    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $permission = new Permission($permission);
        }
        $this->permissions[(string) $permission] = $permission;
    }
    /**
     * {@inheritDoc}
     */
    public function hasPermission($permission)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', (string) $permission));
        $result   = $this->permissions->matching($criteria);
        return count($result) > 0;
    }
    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return !$this->children->isEmpty();
    }
}
