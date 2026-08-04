<?php

use Illuminate\Support\Facades\Schedule;

// Prune stale admin upload staging directories older than 24 hours.
Schedule::command('admin:prune-upload-staging')->daily();
