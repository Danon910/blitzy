<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum HttpMethod: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case HEAD = 'head';
    case DELETE = 'delete';
    case PATCH = 'patch';
    case OPTIONS = 'options';
    case CONNECT = 'connect';
    case TRACE = 'trace';
}
