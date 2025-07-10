<?php

namespace Programic\LaravelKubernetes\Tracing;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Routing\Events\PreparingResponse;
use Illuminate\Routing\Events\ResponsePrepared;
use Illuminate\Routing\Events\RouteMatched;
use Programic\LaravelKubernetes\Tracing\Facades\Tracing;
use RuntimeException;

class EventHandler
{
    protected static $eventHandlerMap = [
        RouteMatched::class => 'routeMatched',
        QueryExecuted::class => 'queryExecuted',
        ResponsePrepared::class => 'responsePrepared',
        PreparingResponse::class => 'preparingResponse',
        TransactionBeginning::class => 'transactionBeginning',
        TransactionCommitted::class => 'transactionCommitted',
        TransactionRolledBack::class => 'transactionRolledBack',
    ];

    /**
     * Attach all event handlers.
     *
     * @uses self::routeMatchedHandler()
     * @uses self::queryExecutedHandler()
     * @uses self::responsePreparedHandler()
     * @uses self::responsePreparingHandler()
     * @uses self::transactionBeginningHandler()
     * @uses self::transactionCommittedHandler()
     * @uses self::transactionRolledBackHandler()
     */
    public function subscribe(Dispatcher $dispatcher): void
    {
        foreach (self::$eventHandlerMap as $eventNane => $handler) {
            $dispatcher->listen($eventNane, [$this, $handler]);
        }
    }

    public function __call(string $method, array $arguments)
    {
        $handlerMethod = "{$method}Handler";

        if (!method_exists($this, $handlerMethod)) {
            throw new RuntimeException("Missing tracing event handler: {$handlerMethod}");
        }

        try {
            $this->{$handlerMethod}(...$arguments);
        } catch (Exception $e) {
            // Ignore to prevent bubbling up errors
        }
    }

    protected function routeMatchedHandler(RouteMatched $event): void
    {
        // Handle the RouteMatched event
        // This is where you can add your custom logic for route matching
        // For example, you might want to log the matched route or perform some tracing
    }

    protected function queryExecutedHandler(QueryExecuted $event): void
    {
        if (!Tracing::currentSpan()) {
            return;
        }

        $span = Tracing::makeSpan('db.sql.query');
        $span->tags([
            'db.name' => $event->connection->getDatabaseName(),
            'db.system' => $event->connection->getDriverName(),
            'db.sql.query' => $event->sql,
            'db.sql.bindings' => $event->bindings,

        ]);
        $span->stop([
            'timestamp' => microtime(true) - $event->time / 1000,
            'duration' => $event->time / 1000,
        ]);
    }

    protected function responsePreparedHandler(ResponsePrepared $event): void
    {
        // Handle the ResponsePrepared event
        // This is where you can add your custom logic for prepared responses
        // For example, you might want to log the response or perform some tracing
    }

    protected function preparingResponseHandler(PreparingResponse $event): void
    {
        // Handle the PreparingResponse event
        // This is where you can add your custom logic for preparing responses
        // For example, you might want to log the response preparation or perform some tracing
    }

    protected function transactionBeginningHandler(TransactionBeginning $event): void
    {
        // Handle the TransactionBeginning event
        // This is where you can add your custom logic for transaction beginnings
        // For example, you might want to log the transaction start or perform some tracing
    }

    protected function transactionCommittedHandler(TransactionCommitted $event): void
    {
        // Handle the TransactionCommitted event
        // This is where you can add your custom logic for committed transactions
        // For example, you might want to log the transaction commit or perform some tracing
    }

    protected function transactionRolledBackHandler(TransactionRolledBack $event): void
    {
        // Handle the TransactionRolledBack event
        // This is where you can add your custom logic for rolled back transactions
        // For example, you might want to log the transaction rollback or perform some tracing
    }
}