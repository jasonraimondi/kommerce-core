<?php
namespace inklabs\kommerce\ActionHandler\Tag;

use inklabs\kommerce\Action\Tag\DeleteTagCommand;
use inklabs\kommerce\tests\Helper\TestCase\ActionTestCase;

class DeleteTagHandlerTest extends ActionTestCase
{
    public function testHandle()
    {
        $tagService = $this->mockService->getTagService();
        $tagService->shouldReceive('delete')
            ->once();

        $command = new DeleteTagCommand(self::UUID_HEX);
        $handler = new DeleteTagHandler($tagService);
        $handler->handle($command);
    }
}
