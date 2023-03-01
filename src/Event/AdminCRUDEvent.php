<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Response;

class AdminCRUDEvent
{
    public const PRE_CREATE  = 'app.entity.pre_create';

    public const POST_CREATE = 'app.entity.post_create';

    public const PRE_EDIT    = 'app.entity.pre_edit';

    public const POST_EDIT   = 'app.entity.post_edit';

    public const PRE_DELETE  = 'app.entity.pre_delete';

    public const POST_DELETE = 'app.entity.post_delete';

    public const SHOW        = 'app.entity.show';

    private mixed $entity;
    private Response $response;

    public function  __construct($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): mixed
    {
        return $this->entity;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}