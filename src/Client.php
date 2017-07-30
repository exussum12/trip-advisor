<?php
namespace exussum12\TripAdvisor;


interface Client
{
    public function getWithSignature($url, $signature);
}
