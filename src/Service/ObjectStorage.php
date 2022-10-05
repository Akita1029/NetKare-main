<?php

namespace App\Service;

use Aws\S3\S3Client;
use Aws\Result;

class ObjectStorage
{
    private $client;

    private $bucket;

    public function __construct(array $args, string $bucket)
    {
        $this->client = new S3Client($args);

        $this->bucket = $bucket;
    }

    public function putObject(string $key, string $body, string $contentType, bool $public = false): Result
    {
        return $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
            'ContentType' => $contentType,
            'ACL' => $public ? 'public-read' : 'private',
        ]);
    }

    public function getObject(string $key): Result
    {
        return $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);
    }

    public function deleteObject(string $key): Result
    {
        return $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }

    public function listObjects(): Result
    {
        return $this->client->listObjects([
            'Bucket' => $this->bucket
        ]);
    }
}