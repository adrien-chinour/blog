<?php

declare(strict_types=1);

namespace App\Domain\Social;

use App\Domain\Blogging\BlogArticleRepository;
use App\Domain\Social\Exception\InvalidCommentException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validates comment data according to domain rules.
 * This is a domain service that enforces business rules using Symfony Validator.
 */
final readonly class CommentValidator
{
    private const MIN_USERNAME_LENGTH = 2;
    private const MAX_USERNAME_LENGTH = 50;
    private const MIN_MESSAGE_LENGTH = 3;
    private const MAX_MESSAGE_LENGTH = 2000;
    private const MAX_MESSAGE_LINES = 50;

    public function __construct(
        private ValidatorInterface $validator
    ) {}

    /**
     * Validates username according to domain rules.
     *
     * @throws InvalidCommentException
     */
    public function validateUsername(string $username): void
    {
        $constraints = [
            new Assert\NotBlank(message: 'Username cannot be blank'),
            new Assert\Length(
                min: self::MIN_USERNAME_LENGTH,
                max: self::MAX_USERNAME_LENGTH,
                minMessage: sprintf('Username must be at least %d characters long', self::MIN_USERNAME_LENGTH),
                maxMessage: sprintf('Username cannot be longer than %d characters', self::MAX_USERNAME_LENGTH),
            ),
            new Assert\Regex(
                pattern: '/^[\p{L}\p{N}\s\-_.]+$/u',
                message: 'Username contains invalid characters. Only letters, numbers, spaces, hyphens, underscores, and dots are allowed.'
            ),
        ];

        $violations = $this->validator->validate($username, $constraints);

        // Additional custom validation: username cannot start or end with whitespace
        if ($username !== trim($username)) {
            $allViolations = new ConstraintViolationList();
            foreach ($violations as $violation) {
                $allViolations->add($violation);
            }
            $allViolations->add(
                new ConstraintViolation(
                    'Username cannot start or end with whitespace',
                    'Username cannot start or end with whitespace',
                    [],
                    null,
                    '',
                    $username
                )
            );
            $violations = $allViolations;
        }

        if ($violations->count() > 0) {
            $this->handleValidationViolations($violations, 'username');
        }
    }

    /**
     * Validates message content according to domain rules.
     *
     * @throws InvalidCommentException
     */
    public function validateMessage(string $message): void
    {
        $constraints = [
            new Assert\NotBlank(message: 'Message cannot be blank'),
            new Assert\Length(
                min: self::MIN_MESSAGE_LENGTH,
                max: self::MAX_MESSAGE_LENGTH,
                minMessage: sprintf('Message must be at least %d characters long', self::MIN_MESSAGE_LENGTH),
                maxMessage: sprintf('Message cannot be longer than %d characters', self::MAX_MESSAGE_LENGTH),
            ),
        ];

        $violations = $this->validator->validate($message, $constraints);

        // Collect all violations in a mutable list
        $allViolations = new ConstraintViolationList();
        foreach ($violations as $violation) {
            $allViolations->add($violation);
        }

        // Additional custom validations: message cannot be only whitespace
        if (trim($message) === '') {
            $allViolations->add(
                new ConstraintViolation(
                    'Message cannot be blank',
                    'Message cannot be blank',
                    [],
                    null,
                    '',
                    $message
                )
            );
        }

        // Additional custom validations
        $lineCount = substr_count($message, "\n") + 1;
        if ($lineCount > self::MAX_MESSAGE_LINES) {
            $allViolations->add(
                new ConstraintViolation(
                    sprintf('Message contains too many lines: %d (max: %d)', $lineCount, self::MAX_MESSAGE_LINES),
                    sprintf('Message contains too many lines: %d (max: %d)', $lineCount, self::MAX_MESSAGE_LINES),
                    [],
                    null,
                    '',
                    $message
                )
            );
        }

        // Check for suspicious patterns (potential injection attempts)
        $this->validateMessageContent($message, $allViolations);

        if ($allViolations->count() > 0) {
            $this->handleValidationViolations($allViolations, 'message');
        }
    }

    /**
     * Validates article identifier exists and is published.
     *
     * @throws InvalidCommentException
     */
    public function validateArticle(BlogArticleRepository $articleRepository, string $articleIdentifier): void
    {
        $article = $articleRepository->getById($articleIdentifier, published: true);

        if ($article === null) {
            throw InvalidCommentException::invalidArticle($articleIdentifier);
        }
    }

    /**
     * Validates message content for suspicious patterns.
     */
    private function validateMessageContent(string $message, ConstraintViolationList $violations): void
    {
        // Check for potential script injection patterns
        $suspiciousPatterns = [
            '/<script/i' => 'Script tags are not allowed',
            '/javascript:/i' => 'JavaScript protocol is not allowed',
            '/on\w+\s*=/i' => 'Event handlers are not allowed',
            '/data:text\/html/i' => 'Data URLs with HTML are not allowed',
            '/vbscript:/i' => 'VBScript protocol is not allowed',
            '/<iframe/i' => 'Iframe tags are not allowed',
            '/<object/i' => 'Object tags are not allowed',
            '/<embed/i' => 'Embed tags are not allowed',
        ];

        foreach ($suspiciousPatterns as $pattern => $errorMessage) {
            if (preg_match($pattern, $message)) {
                $violations->add(
                    new ConstraintViolation(
                        'Message contains potentially dangerous content: ' . $errorMessage,
                        'Message contains potentially dangerous content: ' . $errorMessage,
                        [],
                        null,
                        '',
                        $message
                    )
                );
                return;
            }
        }

        // Check for excessive URL patterns (potential spam)
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/i', $message);
        if ($urlCount > 3) {
            $violations->add(
                new ConstraintViolation(
                    sprintf('Message contains too many URLs: %d (max: 3)', $urlCount),
                    sprintf('Message contains too many URLs: %d (max: 3)', $urlCount),
                    [],
                    null,
                    '',
                    $message
                )
            );
        }
    }

    /**
     * Converts Symfony validation violations to domain exceptions.
     *
     * @throws InvalidCommentException
     */
    private function handleValidationViolations(ConstraintViolationListInterface $violations, string $field): void
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        $errorMessage = implode('; ', $messages);

        match ($field) {
            'username' => throw InvalidCommentException::invalidUsername($errorMessage),
            'message' => throw InvalidCommentException::invalidMessage($errorMessage),
            default => throw InvalidCommentException::invalidMessage($errorMessage),
        };
    }
}
