<?php

namespace Poupouxios\StripeLaravelWebhook\Processors;

interface iPaymentProcessor
{
    public function process($event):bool ;
}