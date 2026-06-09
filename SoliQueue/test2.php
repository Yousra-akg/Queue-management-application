<?php
$notifications = DB::table('notifications')->get();
echo json_encode($notifications);
