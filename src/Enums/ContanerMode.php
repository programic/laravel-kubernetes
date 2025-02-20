<?php

namespace Programic\LaravelKubernetes\Enums;

enum ContanerMode: string
{
    case Http = 'http';
    case Horizon = 'horizon';
    case Worker = 'worker';
    case Scheduler = 'scheduler';
}
