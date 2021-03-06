<?php
namespace inklabs\kommerce\Action\Attachment;

use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\Uuid;
use inklabs\kommerce\Lib\UuidInterface;

class DeleteAttachmentCommand implements CommandInterface
{
    /** @var UuidInterface */
    private $attachmentId;

    /**
     * @param string $attachmentId
     */
    public function __construct($attachmentId)
    {
        $this->attachmentId = Uuid::fromString($attachmentId);
    }

    public function getAttachmentId()
    {
        return $this->attachmentId;
    }
}
