<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generate-typescript-enums {enum?* : One or more enum class names from App\\Enums}')]
#[Description('Generates typescript enums from the PHP Enums')]
class GenerateTypescriptEnums extends Command
{
    public function handle(): void
    {
        try {
            $enums = $this->argument('enum');
            if(empty($enums)){
                foreach(scandir(app_path('Enums')) as $file){
                    if(!str_ends_with($file, '.php')){
                        continue;
                    }
                    $enums[] = str_replace('.php', '', basename($file));
                }
            }

            $dir = resource_path('js/enums');
            if(!is_dir($dir)){
                mkdir($dir, 0755, true);
            }

            $exports = [];
            foreach($enums as $enumName){
                $class = "App\\Enums\\{$enumName}";

                if(!enum_exists($class)){
                    $this->error("Enum {$class} not found.");
                    continue;
                }

                $fileContent = "export enum {$enumName} {".PHP_EOL;
                foreach($class::cases() as $case){
                    $name = $case->name;
                    $value = is_string($case->value) ? "'{$case->value}'" : $case->value;
                    $fileContent .= "   {$name} = {$value},".PHP_EOL;
                }
                $fileContent .= "}";

                $filename = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $enumName));
                file_put_contents("{$dir}/{$filename}.ts", $fileContent);
                $exports[] = "export { {$enumName} } from './{$filename}';";

                $this->info("{$filename}.ts was successfully created.");
            }

            file_put_contents("{$dir}/index.ts", implode(PHP_EOL, $exports).PHP_EOL);
            $this->info("Generated typescript enums successfully.");
        } catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
