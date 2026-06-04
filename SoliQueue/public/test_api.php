<?php
$response = file_get_contents("http://127.0.0.1:8000/api/mobile/sessions/15/queue?candidate_id=20");
echo "RESPONSE:\n";
echo $response;
