<?php

return [
    'ice_servers' => json_decode((string) env('WEBRTC_ICE_SERVERS', '[]'), true) ?: [],
];
