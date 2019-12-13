<?php

return [
    'timeout' => env('WAITINGJOB_TIMEOUT', 10), // Timeout waiting result job in seconds
    'ttl'     => env('WAITINGJOB_TTL', 1) // Cache ttl in Minutes
];