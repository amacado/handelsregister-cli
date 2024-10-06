<?php

    namespace App\Contracts;

    /**
     * Throwable implementing this contract can be rendered in the console.
     */
    interface RenderableExceptionContract extends \Throwable
    {
        /**
         * Render the exception in the console.
         *
         * @return void
         */
        public function render(): void;
    }
