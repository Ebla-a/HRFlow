<?php
namespace Modules\AI\Services;

use Exception;
use Modules\AI\Contracts\AiToolInterface;

class AiToolRegistry
{
    private array $tools = [];
    /**
     * Summary of register
     * @param AiToolInterface $tool
     * @return void
     */
    public function register(AiToolInterface $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }
    /**
     * Summary of getDeclarationsForGemini
     * @return array[]
     */
    public function getDeclarationsForGemini(): array
    {
        $functions = [];
        foreach ($this->tools as $tool) {
            $functions[] = [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'parameters' => $tool->parameters(),
            ];
        }

        return [
            ['function_declarations' => $functions]
        ];
    }
    /**
     * Summary of execute
     * @param string $name
     * @param array $arguments
     * @param int $userId
     * @throws Exception
     * @return array
     */
    public function execute(string $name, array $arguments, int $userId): array
    {
        if (!isset($this->tools[$name])) {
            throw new Exception("this tool {$name} is not recorded  .");
        }

        return $this->tools[$name]->execute($arguments, $userId);
    }
}
