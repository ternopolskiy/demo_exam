<?php

require_once __DIR__ . '/../bootstrap.php';

auth()->logout();
redirect('../../index.php');
