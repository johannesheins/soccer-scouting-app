<?php

namespace App\Console\Commands;

use App\Enums\RightEnum;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generate-typescript-right-enum')]
#[Description('Generates resources/js/enums/right-enum.ts from the PHP RightEnum')]
class GenerateTypescriptRightEnum extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $dir = resource_path('js/enums');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $fileContent = "export enum RightEnum {".PHP_EOL;
            foreach(RightEnum::cases() as $enum){
                $fileContent .= "   {$enum->name} = {$enum->value},".PHP_EOL;
            }
            $fileContent .= "}";

            file_put_contents("{$dir}/right-enum.ts", $fileContent);
            file_put_contents("{$dir}/index.ts", "export { RightEnum } from './right-enum';".PHP_EOL);

            echo 'The right-enum.ts was successfully created'.PHP_EOL;
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }


}
