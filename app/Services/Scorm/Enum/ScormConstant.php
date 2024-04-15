<?php

namespace App\Services\Scorm\Enum;

enum ScormConstant: string
{
    case MANIFEST_FILE_NAME = 'imsmanifest.xml';

    case SCHEMA_VERSION_TAG = 'schemaversion';
}
