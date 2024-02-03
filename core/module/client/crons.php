<?php

foreach ( glob(__DIR__.'/cron/*.php') as $cron ) {
  require $cron;
}