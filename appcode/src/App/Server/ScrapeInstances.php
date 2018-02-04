<?php
namespace App\Server;


class ScrapeInstances
{
    protected $awsClient;
    
    protected $queueRepository;
    
    protected $instanceName;
    
    protected $maxThreshold;
    
    protected $amiImageId;
    
    protected $securityGroups;
    
    protected $instanceType;
    
    protected $keyPairName;
    
    public function __construct($awsClient, $queueRepository, array $config)
    {
        $this->setAwsClient($awsClient);
        $this->setQueueRepository($queueRepository);
        $this->setInstanceName($config['instanceName']);
        $this->setMaxThreshold($config['maxThreshold']);
        $this->setAmiImageId($config['amiImageId']);
        $this->setSecurityGroups($config['securityGroups']);
        $this->setInstanceType($config['instanceType']);
        $this->setKeyPairName($config['keyPairName']);
    }
    
    public function setAwsClient($awsClient)
    {
        $this->awsClient = $awsClient;
        return $this;
    }
    
    public function getAwsClient()
    {
        return $this->awsClient;
    }
    
    public function setQueueRepository($queueRepository)
    {
        $this->queueRepository = $queueRepository;
        return $this;
    }
    
    public function getQueueRepository()
    {
        return $this->queueRepository;
    }
    
    public function setInstanceName($instanceName)
    {
        $this->instanceName = $instanceName;
        return $this;
    }
    
    public function getInstanceName() : string
    {
        return $this->instanceName;
    }
    
    public function setMaxThreshold(int $maxThreshold)
    {
        $this->maxThreshold = $maxThreshold;
        return $this;
    }
    
    public function getMaxThreshold() : int
    {
        return $this->maxThreshold;
    }
    
    public function setAmiImageId($amiImageId)
    {
        $this->amiImageId = $amiImageId;
        return $this;
    }
    
    public function getAmiImageId() : string
    {
        return $this->amiImageId;
    }
    
    public function setSecurityGroups(array $securityGroups)
    {
        $this->securityGroups = $securityGroups;
        return $this;
    }
    
    public function getSecurityGroups() : array
    {
        return $this->securityGroups;
    }
    
    public function setInstanceType($instanceType)
    {
        $this->instanceType = $instanceType;
        return $this;
    }
    
    public function getInstanceType() : string
    {
        return $this->instanceType;
    }
    
    public function setKeyPairName($keyPairName)
    {
        $this->keyPairName = $keyPairName;
        return $this;
    }
    
    public function getKeyPairName() : string
    {
        return $this->keyPairName;
    }
    
    public function process()
    {
        $count = $this->getQueueRepository()->count();
        
        $numberOfInstances = ceil($count / 500);
        
        if ($numberOfInstances > $this->getMaxThreshold()) {
            $numberOfInstances = $this->getMaxThreshold();
        }
        
        $currentInstances = $this->countCurrentInstances();
        
        $countOfInstances = (int) ($numberOfInstances - $currentInstances);
        
        if ($countOfInstances > 0) {
            $this->launchInstances($countOfInstances);
        } elseif ($countOfInstances < 0) {
            $this->shutdownInstances(abs($countOfInstances));
        }
        
        return $this;
    }
    
    protected function checkCurrentInstances()
    {
        $client = $this->getAwsClient();
        
        $servers = $client->describeInstances([
            'Filters' => [
                [
                    'Name' => 'tag-value', 
                    'Values' => [$this->getInstanceName()],
                ],
            ],
        ]);
        
        return $servers;
    }
    
    protected function countCurrentInstances()
    {
        $servers = $this->checkCurrentInstances();
        
        $countRunning = 0;
        
        foreach ($servers['Reservations'] as $reservation) {
            foreach ($reservation['Instances'] as $instance) {
                switch ($instance['State']['Code']) {
                    case 16:
                        $countRunning++;
                        break;
                    default;
                }
            }
        }
        return $countRunning;
    }
    
    protected function launchInstances(int $count)
    {
        $client = $this->getAwsClient();
        
        $result = $client->runInstances([
            'ImageId'        => $this->getAmiImageId(),
            'MinCount'       => $count,
            'MaxCount'       => $count,
            'InstanceType'   => $this->getInstanceType(),
            'KeyName'        => $this->getKeyPairName(),
            'SecurityGroups' => $this->getSecurityGroups(),
            'TagSpecifications' => [
                [
                    'ResourceType' => 'instance',
                    'Tags' => [
                        [
                            'Key' => 'Name',
                            'Value' => $this->getInstanceName(),
                        ],
                    ],
                ],
            ],
        ]);
        
        return $result;
    }
    
    protected function shutdownInstances($countOfInstances)
    {
        $instanceIds = [];
        $servers = $this->checkCurrentInstances();
        
        foreach ($servers['Reservations'] as $reservation) {
            foreach ($reservation['Instances'] as $instance) {
                if ($instance['State']['Code'] === 16) {
                    $instanceIds[] = $instance['InstanceId'];
                    if (count($instanceIds) === $countOfInstances) {
                        break;
                    }
                }
            }
        }
        
        $client = $this->getAwsClient();
        
        $result = $client->terminateInstances([
            'InstanceIds' => $instanceIds,
        ]);
        
        return $result;
    }
    
}