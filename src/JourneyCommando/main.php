<?php

    // Run in CLI mode only
    if(php_sapi_name() === 'cli')
    {
        require("ppm");
        \ppm\ppm::import("net.intellivoid.journey_commando");

        $JourneyCommando = new \JourneyCommando\JourneyCommando();
        $JourneyCommando->execute();
    }