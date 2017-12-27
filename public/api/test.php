<?php

header( 'Content-Type: application/json' );
echo json_encode( array_merge( $_POST, $_FILES ) );