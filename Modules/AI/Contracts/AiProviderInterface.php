<?php
namespace Modules\AI\Contracts;

interface AiProviderInterface
{
    /**
     * Summary of generateContent
     * @param array $contents
     * @param array $tools
     * @return void
     */

//for send request for ai with tools
    public function generateContent(array $contents, array $tools = []): array;
}
