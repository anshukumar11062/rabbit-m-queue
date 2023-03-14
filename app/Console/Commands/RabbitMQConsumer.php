<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class RabbitMQConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {queue} {--timeout=60}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from a RabbitMQ queue.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $queue = $this->argument('queue');
        $timeout = $this->option('timeout');

        $connection = app(RabbitMQQueue::class)->getConnection();

        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $callback = function ($message) use ($channel) {
            $payload = json_decode($message->body, true);

            // process the message here

            $channel->basic_ack($message->delivery_info['delivery_tag']);
        };

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait(null, false, $timeout);
        }

        $channel->close();
        $connection->close();
    }
}
