<?php
namespace inklabs\kommerce\Action\Product\Query;

use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\Lib\Uuid;
use inklabs\kommerce\Lib\UuidInterface;

final class GetProductsByTagRequest
{
    /** @var UuidInterface */
    private $tagId;

    /** @var PaginationDTO */
    private $paginationDTO;

    /**
     * @param string $tagId
     * @param PaginationDTO $paginationDTO
     */
    public function __construct($tagId, PaginationDTO $paginationDTO)
    {
        $this->tagId = Uuid::fromString($tagId);
        $this->paginationDTO = $paginationDTO;
    }

    public function getTagId()
    {
        return $this->tagId;
    }

    public function getPaginationDTO()
    {
        return $this->paginationDTO;
    }
}
