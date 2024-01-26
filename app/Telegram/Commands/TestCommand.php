<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class TestCommand extends Command
{
    protected string $name = 'test';
    protected array $aliases = ['testing'];
    protected string $description = 'Test a bot';
    protected string $pattern = '{username} {age: \d+}';

    public function handle()
    {
        # username from Update object to be used as fallback.
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;

        Log::info('message', $this->getUpdate()->getMessage()->toArray());

        # Get the username argument if the user provides,
        # (optional) fallback to username from Update object as the default.
        $username = $this->argument(
            'username',
            $fallbackUsername
        );

        $this->replyWithMessage([
            'text' => "Hello {$username}!"
        ]);

        # This will update the chat status to "typing..."
        //$this->replyWithChatAction(['action' => Actions::TYPING]);

        /*
        # Get all the registered commands.
        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);

        if($this->argument('age', 0) >= 18) {
            $this->replyWithMessage(['text' => 'Congrats, You are eligible to buy premimum access to our membership!']);
        } else {
            $this->replyWithMessage(['text' => 'Sorry, you are not eligible to access premium membership yet!']);
        }
        */
    }
}
