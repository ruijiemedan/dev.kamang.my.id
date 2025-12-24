<?php


namespace App\Services;


use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;


class MikrotikService
{
protected Client $client;


public function __construct()
{
$config = new Config([
'host' => env('MIKROTIK_HOST'),
'user' => env('MIKROTIK_USER'),
'pass' => env('MIKROTIK_PASS'),
'port' => (int) env('MIKROTIK_PORT', 8728),
'timeout' => 10,
'attempts' => 1,
]);


$this->client = new Client($config);
}


/**
* Interface List
*/
public function interfaces(): array
{
$query = new Query('/interface/print');
return $this->client->query($query)->read();
}


/**
* IP Address List
*/
public function ipAddresses(): array
{
$query = new Query('/ip/address/print');
return $this->client->query($query)->read();
}


/**
* IP Neighbours
*/
public function neighbours(): array
{
$query = new Query('/ip/neighbor/print');
return $this->client->query($query)->read();
}


/**
* Traffic Monitor (once)
*/
public function trafficMonitor(string $interface): array
{
$query = (new Query('/interface/monitor-traffic'))
->equal('interface', $interface)
->equal('once', '');


return $this->client->query($query)->read();
}
}
