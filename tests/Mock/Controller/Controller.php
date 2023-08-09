<?php
declare(strict_types=1);

namespace Tests\Mock\Controller;

use Fyre\Server\ClientResponse;

abstract class Controller
{

    public function invokeAction(string $action, array $args = []): static
    {
        return $this;
    }

    public function getResponse(): ClientResponse
    {
        return new ClientResponse();
    }

}
