<?php

namespace Core;

use Monolog\Level;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    private string $logfile = "changes.log";

    private string $logfolder = '../logs/';

    private MonoLogger $log;

    public function __construct(string $channel = 'app')
    {
        $timezone = new Settings('app');
        date_default_timezone_set($timezone->get('timezone'));

        $this->log = new MonoLogger($channel);
        $this->log->pushHandler(new StreamHandler(
            $this->logfolder . $this->logfile,
            Level::Debug
        ));
    }

    public function setLogfile($filename)
    {
        $this->logfile = $filename;
    }

    public function save(string|\Stringable $message, array $context = [], string $level = 'debug'): void
    {
        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

        if (!in_array($level, $levels)) {
            throw new \InvalidArgumentException("Argument {$level} not supported.");
        }

        $this->log->{$level}($message, $context);
    }
}