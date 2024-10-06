<?php

    namespace App\Exceptions;

    use App\Contracts\RenderableExceptionContract;
    use Illuminate\Support\Collection;
    use Throwable;
    use function Termwind\render;

    class RenderableConsoleException extends \Exception implements RenderableExceptionContract
    {
        protected Collection $messages;

        public function __construct(string|iterable $message = "", int $code = 0, ?Throwable $previous = null)
        {
            $this->messages = collect($message);

            parent::__construct($this->getMessages()->join('; '), $code, $previous);
        }

        public function render(): void
        {
            render('<div class="bg-orange-800 p-1"><ul>' . $this->getMessages()->map(static fn($message) => '<li>' . $message . '</li>')->join('') . '</ul></div>');
        }

        protected function getMessages(): Collection
        {
            return $this->messages;
        }
    }
