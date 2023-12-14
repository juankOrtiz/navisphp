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

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log->debug($message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log->info($message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log->notice($message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log->warning($message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log->error($message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log->critical($message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log->alert($message, $context);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log->emergency($message, $context);
    }
}