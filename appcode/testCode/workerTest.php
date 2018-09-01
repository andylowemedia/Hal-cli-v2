<?php
// tick use required as of PHP 4.3.0
declare(ticks = 1);

$breakout = false;

// signal handler function
function sig_handler($signo)
{
    global $breakout;
    
    switch ($signo) {
        case SIGINT:
        case SIGTERM:
            $breakout = true;
            break;
        case SIGKILL:
            exit;
            break;
        default:
    }
}

echo "Installing signal handler...\n";

// setup signal handlers
pcntl_signal(SIGINT, "sig_handler");
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");

// or use an object, available as of PHP 4.3.0
// pcntl_signal(SIGUSR1, array($obj, "do_something"));

echo "Generating signal SIGUSR1 to self...\n";

// send SIGUSR1 to current process id
// posix_* functions require the posix extension
while (true) {
    if ($breakout) {
        break;
    }
    sleep(1);
};

echo "Done\n";
