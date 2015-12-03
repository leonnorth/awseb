<?php

// Include the composer autoloader
require_once 'vendor/autoload.php';
use Aws\S3\S3Client;
// Get the post body (This is the content
// of the SQS message)
$postBody = file_get_contents('php://input');


$job = json_decode($postBody);


// Do some work here with the job (resize image etc.)
$client = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-southeast-2'
]);

$bucket = "leontestbucket2";
$key = 'leon_sqs_notificiation_test1.txt';
$result = $client->putObject(array(
    'Bucket' => $bucket,
    'Key'    => $key,
    'Body'   => "leon_sqs_notificiation_test1"
));



if ($error) {
    // Returning any non 200 HTTP code will tell the
    // SQS queue that the job failed and to re-queue it.
    http_response_code(500);
}
