<?php
require_once 'vendor/autoload.php';
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

$queueUrl = "https://sqs.eu-west-1.amazonaws.com/540688370389/the-hal-project-v2-job-queue";
$client = new SqsClient([
    'profile' => 'default',
    'region' => 'eu-west-1',
    'version' => '2012-11-05'
]);
$result = $client->GetQueueAttributes(['AttributeNames' => ['All'],'QueueUrl' => $queueUrl]);
print_r($result->get('Attributes')['ApproximateNumberOfMessages'] . "\n");
die;
