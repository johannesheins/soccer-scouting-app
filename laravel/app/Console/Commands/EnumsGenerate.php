<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('enums:generate')]
#[Description('Generates typescript enums from the PHP Enums')]
class EnumsGenerate extends Command
{
    public const string BASE_DIR = 'Enums';

    public const string RESOURCE_DIR = 'js/enums';

    public function handle(): void
    {
        try {
            $enums = $this->getEnumNames(app_path(self::BASE_DIR));
            $basePath = resource_path(self::RESOURCE_DIR);

            if(! is_dir($basePath)){
                mkdir($basePath, 0755, true);
            }

            $exports = [];
            foreach($enums as $enumName){
                try {
                    $exports[] = $this->writeEnum($enumName, $basePath);
                } catch(Exception $exception){
                    $this->error($exception->getMessage());
                }
            }

            file_put_contents("{$basePath}/index.ts", implode(PHP_EOL, $exports).PHP_EOL);
            $this->info('Generated typescript enums successfully.');
        } catch(Exception $e){
            $this->error($e->getMessage());
        }
    }

    private function writeEnum(string $enumName, string $basePath): string
    {
        $className = basename($enumName);
        $dirName = dirname($enumName);
        $subDir = $this->camelToKebabCase(trim($dirName, '/'));
        $fileName = $this->camelToKebabCase($className);
        $targetDir = $subDir !== '' ? "{$basePath}/{$subDir}" : $basePath;

        if(!is_dir($targetDir)){
            mkdir($targetDir, 0755, true);
        }

        file_put_contents("{$targetDir}/{$fileName}.ts", $this->generateEnum($enumName, $className));
        $importPath = $subDir !== '' ? "./{$subDir}/{$fileName}" : "./{$fileName}";

        return "export { {$className} } from '{$importPath}';";
    }

    private function getEnumNames(string $dirName, array &$enums = []): array
    {
        foreach(scandir($dirName) as $file){
            if($file === '.' || $file === '..'){
                continue;
            }

            $path = "{$dirName}/{$file}";

            if(is_dir($path)){
                $this->getEnumNames($path, $enums);

                continue;
            }

            if(! str_ends_with($file, 'Enum.php')){
                continue;
            }

            $file = str_replace('.php', '', $file);
            $enums[] = str_replace(app_path(self::BASE_DIR), '', "{$dirName}/{$file}");
        }

        return $enums;
    }

    private function generateEnum(string $enumName, string $className): string
    {
        $class = 'App\\Enums'.str_replace('/', '\\', $enumName);

        if(! enum_exists($class)){
            throw new Exception("Enum {$class} not found.");
        }

        $fileContent = "export enum {$className} {".PHP_EOL;
        foreach($class::cases() as $case){
            $value = is_string($case->value) ? "'{$case->value}'" : $case->value;
            $fileContent .= "    {$case->name} = {$value},".PHP_EOL;
        }
        $fileContent .= '}';

        return $fileContent;
    }

    private function camelToKebabCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }
}
