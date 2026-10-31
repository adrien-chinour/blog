<?php

declare(strict_types=1);

namespace App\Domain\Blogging\Exception;

use DomainException;

final class BlogArticleIndexationFailedException extends DomainException {}
