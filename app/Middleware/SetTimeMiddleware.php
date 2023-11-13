<?php

    class SetTimeMiddleware
    {
        public function __invoke($request,  $handler)
        {
            date_default_timezone_set('america/argentina/buenos_aires');
            return $handler->handle($request);
        }
    }

?>