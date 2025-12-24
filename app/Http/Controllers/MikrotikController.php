<?php


namespace App\Http\Controllers;


use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;


class MikrotikController extends Controller
{
protected MikrotikService $mikrotik;


public function __construct(MikrotikService $mikrotik)
{
$this->mikrotik = $mikrotik;
}


public function interfaces(): JsonResponse
{
return response()->json([
'status' => 'ok',
'data' => $this->mikrotik->interfaces(),
]);
}


public function ipAddresses(): JsonResponse
{
return response()->json([
'status' => 'ok',
'data' => $this->mikrotik->ipAddresses(),
]);
}


public function neighbours(): JsonResponse
{
return response()->json([
'status' => 'ok',
'data' => $this->mikrotik->neighbours(),
]);
}


public function traffic(string $interface): JsonResponse
{
return response()->json([
'status' => 'ok',
'interface' => $interface,
'data' => $this->mikrotik->trafficMonitor($interface),
]);
}
}
