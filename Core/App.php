<?php

namespace Core;

class App
{
  protected static Container $container;

  public static function setContainer(Container $container): void {
    static::$container = $container;
  }

  public static function container(): Container {
    return static::$container;
  }

  public static function bind($key, $resolver): void {
    static::container()->bind($key, $resolver);
  }

  public static function resolve($key) {
    return static::container()->resolve($key);
  }
}