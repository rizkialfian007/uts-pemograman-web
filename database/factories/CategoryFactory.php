<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

{
    Factory::guessFactoryNamesUsing(
        fn (string $modelName) => 'Database\\Factories\\'.class_basename($modelName).'Factory'
    );
}
