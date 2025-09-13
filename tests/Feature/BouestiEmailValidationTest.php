<?php

use App\Rules\BouestiEmail;
use Illuminate\Support\Facades\Validator;

test('validates student email format', function () {
    $validator = Validator::make(['email' => 'john.1234@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('student')]
    ]);
    
    expect($validator->passes())->toBeTrue();
    
    $validator = Validator::make(['email' => 'mary.5678@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('student')]
    ]);
    
    expect($validator->passes())->toBeTrue();
});

test('rejects invalid student email format', function () {
    $validator = Validator::make(['email' => 'john@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('student')]
    ]);
    
    expect($validator->fails())->toBeTrue();
    
    $validator = Validator::make(['email' => 'john.doe@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('student')]
    ]);
    
    expect($validator->fails())->toBeTrue();
});

test('validates staff email format', function () {
    $validator = Validator::make(['email' => 'john.doe@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('staff')]
    ]);
    
    expect($validator->passes())->toBeTrue();
    
    $validator = Validator::make(['email' => 'mary.smith@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('staff')]
    ]);
    
    expect($validator->passes())->toBeTrue();
});

test('rejects invalid staff email format', function () {
    $validator = Validator::make(['email' => 'john.1234@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('staff')]
    ]);
    
    expect($validator->fails())->toBeTrue();
    
    $validator = Validator::make(['email' => 'john@bouesti.edu.ng'], [
        'email' => [new BouestiEmail('staff')]
    ]);
    
    expect($validator->fails())->toBeTrue();
});
