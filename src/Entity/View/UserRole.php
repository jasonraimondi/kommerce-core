<?php
namespace inklabs\kommerce\Entity\View;

use inklabs\kommerce\Entity as Entity;
use inklabs\kommerce\Lib as Lib;

class UserRole
{
    public $id;
    public $name;
    public $description;
    public $created;
    public $updated;

    public function __construct(Entity\UserRole $userRole)
    {
        $this->id          = $userRole->getId();
        $this->name        = $userRole->getName();
        $this->description = $userRole->getDescription();
        $this->created     = $userRole->getCreated();
        $this->updated     = $userRole->getUpdated();
    }
}