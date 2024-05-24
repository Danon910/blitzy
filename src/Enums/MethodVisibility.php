<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum MethodVisibility: string
{
    case PRIVATE = 'private';
    case PROTECTED = 'protected';
    case PUBLIC = 'public';
}
