<?php

    use acm\acm;
    use acm\Objects\Schema;

    $this->acm = new acm(__DIR__, 'JourneyCommando');

    $DatabaseSchema = new Schema();
    $DatabaseSchema->setDefinition("Host", "localhost");
    $DatabaseSchema->setDefinition("Port", "3306");
    $DatabaseSchema->setDefinition("Username", "root");
    $DatabaseSchema->setDefinition("Password", "");
    $DatabaseSchema->setDefinition("Name", "intellivoid");
    $this->acm->defineSchema("Database", $DatabaseSchema);


    $ConfigurationSchema = new Schema();
    $ConfigurationSchema->setDefinition("Interval", 1500); // 25 Minutes
    $ConfigurationSchema->setDefinition("DatabaseCleanupEnabled", true); // Clears unused data from the database
    $ConfigurationSchema->setDefinition("OpenBluSyncEnabled", true); // Syncs the OpenBlu servers
    $this->acm->defineSchema("Configuration", $ConfigurationSchema);