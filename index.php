<?php

/*
 * This performs a pseudo etl transform
 */

// Include the composer autoloader
require_once 'vendor/autoload.php';
use Aws\S3\S3Client;

// Capture the post body
$postBody = file_get_contents('php://input');

// Decode the message
$job = json_decode($postBody);

// Connect to the client
$client = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-southeast-2'
]);

// Read the bi_queue file
$be_client = $job->{'Records'}[0]->{'s3'}->{'bucket'}->{'name'};
$key = $job->{'Records'}[0]->{'s3'}->{'object'}->{'key'};
$result_bi_queue = $client->getObject(array(
    'Bucket' => $be_client,
    'Key'    => $key
));

// Read the mapping file
$result_mapping = $client->getObject(array(
    'Bucket' => "be-data",
    'Key'    => "etl_mappings/{$be_client}/{$be_client}_mapping.csv"
));

// Combine the body of the files
$etl_body = $result_bi_queue['Body'] . $result_mapping['Body'];

// Write the new file
$result = $client->putObject(array(
    'Bucket' => $be_client,
    'Key'    => "etl/{$be_client}_etl_xform.csv",
    'Body'   => $etl_body
));



if ($error) {
    // Returning any non 200 HTTP code will tell the
    // SQS queue that the job failed and to re-queue it.
    http_response_code(500);
}
